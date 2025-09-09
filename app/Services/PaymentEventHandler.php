<?php

namespace App\Services;

use App\Clients\BiletAvtoApiClient;
use App\Jobs\SendRefundMailJob;
use App\Jobs\SendTicketsJob;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use App\Services\Atol\AtolReceipt;
use App\Services\Atol\AtolService;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use YooKassa\Client;
use YooKassa\Common\Exceptions\ApiException;
use YooKassa\Common\Exceptions\BadApiRequestException;
use YooKassa\Common\Exceptions\ExtensionNotFoundException;
use YooKassa\Common\Exceptions\ForbiddenException;
use YooKassa\Common\Exceptions\InternalServerError;
use YooKassa\Common\Exceptions\NotFoundException;
use YooKassa\Common\Exceptions\ResponseProcessingException;
use YooKassa\Common\Exceptions\TooManyRequestsException;
use YooKassa\Common\Exceptions\UnauthorizedException;
use YooKassa\Request\Payments\CancelResponse;
use YooKassa\Request\Payments\CreateCaptureResponse;

class PaymentEventHandler
{
    protected BiletAvtoApiClient $apiClient;
    protected Client $yookassaClient;
    protected AtolService $atolService;

    public function __construct(BiletAvtoApiClient $apiClient, Client $yookassaClient, AtolService $atolService)
    {
        $this->apiClient = $apiClient;
        $this->yookassaClient = $yookassaClient;
        $this->atolService = $atolService;
    }

    public function handlePaymentSucceeded(array $requestData): JsonResponse
    {
        $order = $this->getOrderFromRequest($requestData);
        if (! $order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }



        DB::beginTransaction();
        try {
            $payment = $this->updatePaymentStatus($order, 'succeeded');
            $this->updateOrderStatus($order, 'confirmed');
            $response = $this->processTickets($order);

            // Проверка, что ответ был успешным
            if (isset($response['success']) && $response['success'] === true) {
                if (! empty($response['data'])) {
                    foreach ($response['data'] as $data) {
                        // Обновляем статус билета, если найден
                        $ticket = $order->tickets()->where('ba_ticket_id', $data['ticketId'])->first();
                        if ($ticket) {
                            $ticket->status = 'confirmed';
                            $ticket->ticket_url = $data['url_ticket'];
                            $ticket->save();
                        } else {
                            Log::warning(
                                'Ticket not found for ba_ticket_id',
                                ['ba_ticket_id' => $data['ticketId']]
                            );
                        }
                    }
                } else {
                    Log::error('No ticket data found in the response', ['response' => $response]);
                }
            } else {
                Log::error('BiletAvto buying failed or invalid response', ['response' => $response]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Transaction failed', ['error' => $e->getMessage()]);

            return response()->json(['status' => 'error', 'message' => 'Transaction failed'], 500);
        }
        // Отправка задачи на очередь
        SendTicketsJob::dispatch($order)->onQueue('tickets-send');

        $operationType = 'sell';
        $receipt = new AtolReceipt();
        foreach ($order->tickets as $ticket) {
            $receipt->addItem('Билет №' . $ticket->id, $ticket->nominal);
            $receipt->addItem('Сервисный сбор за билет №' . $ticket->id, $ticket->price - $ticket->nominal);
        }

        $receiptData = $receipt->getData($order->uuid, $operationType, $order->user->email);
        $atolResponse = $this->atolService->registerDocument($operationType, $receiptData);

        // Логируем ответ от Атол
        Log::info('Atol response', ['response' => $atolResponse]);


        // Возвращаем успешный ответ
        return response()->json([
            'status' => 'success',
            'order' => $order->toArray(),
            'payment' => $payment->toArray(),
            'response' => $response,
        ]);
    }

    /**
     * @throws ResponseProcessingException
     * @throws BadApiRequestException
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws NotFoundException
     * @throws ApiException
     * @throws ExtensionNotFoundException
     * @throws InternalServerError
     */
    public function handleWaitingForCapture(array $requestData): JsonResponse
    {
        $order = $this->getOrderFromRequest($requestData);
        if (! $order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }
        $this->updatePaymentStatus($order, 'waiting_for_capture');
        $orderCreatedAt = $order->created_at;
        $currentTime = now();

        $response = $currentTime->diffInMinutes($orderCreatedAt, true) > 10
            ? $this->cancelPayment($requestData['object']['id'])
            : $this->capturePayment($requestData['object']);

        return response()->json([
            'status' => 'success',
            'order' => $order,
            'response' => $response,
        ]);
    }

    /**
     * @throws ConnectionException
     */
    public function handlePaymentCanceled(array $requestData): JsonResponse
    {
        $order = $this->getOrderFromRequest($requestData);
        if (! $order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        $this->updateOrderStatus($order, 'canceled');
        $this->cancelTickets($order);
        $this->updatePaymentStatus($order, 'canceled');

        return response()->json(['status' => 'success']);
    }

    /**
     * @throws ConnectionException
     */
    public function handleRefundSucceeded(array $requestData): JsonResponse
    {
        $ticketId = $this->getTicketIdFromRefund($requestData);
        $ticket = Ticket::findOrFail($ticketId);

        try {
            $this->apiClient->refund($ticket->ba_ticket_id);
            $this->updateTicketStatus($ticket, 'refunded', $requestData);
            $operationType = 'sell_refund';

            $receipt = new AtolReceipt();
            $receipt->addItem('Возврат билета №' . $ticket->id, $ticket->refunded_amount);

            $receiptData = $receipt->getData($ticket->uuid, $operationType, $ticket->order->user->email);
            $atolResponse = $this->atolService->registerDocument($operationType, $receiptData);

            // Логируем ответ от Атол
            Log::info('Atol refund response', ['response' => $atolResponse]);

//            SendRefundMailJob::dispatch($ticket, 'admin', $requestData['object']['description'], $ticket->refunded_amount)->onQueue('refund');
        } catch (Exception $e) {
            Log::critical('Неожиданная ошибка при возврате платежа', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }

        return response()->json(['status' => 'success']);
    }

    private function getOrderFromRequest(array $requestData): ?Order
    {
        $orderId = $requestData['object']['metadata']['order_id'] ?? null;

        return $orderId ? Order::find($orderId) : null;
    }

    private function updatePaymentStatus(Order $order, string $status): Payment
    {
        $payment = $order->payment()->firstOrFail();
        $payment->status = $status;
        $payment->save();

        return $payment;
    }

    private function updateOrderStatus(Order $order, string $status): void
    {
        $order->status = $status;
        $order->save();
    }

    /**
     * @throws ConnectionException
     */
    private function processTickets(Order $order): array
    {
        $ticketsData = $order->tickets->map(function ($ticket) {
            return [
                'ticket_id' => $ticket->ba_ticket_id,
                'agentPriceApi' => $ticket->price,
            ];
        })->toArray();

        return $this->apiClient->buy($order->ba_operation_id, $ticketsData);
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    private function cancelTickets(Order $order): void
    {
        foreach ($order->tickets as $ticket) {
            // Отправляем запрос на отмену билета
            $response = $this->apiClient->cancelTicket($ticket->ba_ticket_id);

            // Проверяем, что ответ — это массив и существует нужный ключ
            if (is_array($response)) {
                // Если структура ответа с ключом success и success = true
                if (isset($response['success']) && $response['success'] === true) {
                    // Можно просто считать, что операция успешна
                    Log::info("Ticket {$ticket->id} successfully canceled: " . $response['message']);
                }
                // Или если структура другая с success_msg
                elseif (isset($response['success']['success_msg']) && $response['success']['success_msg'] === 'Операция выполнена успешно') {
                    // Если операция выполнена успешно
                    Log::info("Ticket {$ticket->id} successfully canceled: " . $response['success']['success_msg']);
                } else {
                    throw new Exception('Failed to cancel ticket: ' . $ticket->id . '. Response: ' . json_encode($response));
                }
            } else {
                Log::error('Unexpected response format', ['response' => $response, 'ticket_id' => $ticket->id]);
                throw new Exception('Unexpected response format for ticket: ' . $ticket->id);
            }

            // Обновляем статус билета
            $ticket->update(['status' => 'canceled']);
        }
    }

    private function getTicketIdFromRefund(array $requestData): int
    {
        $description = $requestData['object']['description'] ?? '';

        return (int) str_replace('Возврат билета №', '', $description);
    }

    /**
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws InternalServerError
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    private function cancelPayment(string $yookassaPaymentId): ?CancelResponse
    {
        try {
            return $this->yookassaClient->cancelPayment($yookassaPaymentId, uniqid('', true));
        } catch (
            BadApiRequestException |
            ForbiddenException |
            InternalServerError |
            NotFoundException |
            ResponseProcessingException |
            TooManyRequestsException |
            UnauthorizedException |
            ApiException |
            ExtensionNotFoundException $e
        ) {
            // Логирование ошибки
            Log::error('Ошибка при попытке отмены платежа', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'payment_id' => $yookassaPaymentId,
            ]);

            // Возврат null в случае ошибки
            return null;
        } catch (Exception $e) {
            // Логирование неожиданных ошибок
            Log::critical('Неожиданная ошибка при отмене платежа', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'payment_id' => $yookassaPaymentId,
            ]);

            // Бросить исключение выше, если необходимо
            throw $e;
        }
    }

    /**
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws InternalServerError
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws Exception
     */
    private function capturePayment(array $yookassaPayment): ?CreateCaptureResponse
    {
        try {
            return $this->yookassaClient->capturePayment([
                'amount' => $yookassaPayment['amount'],
            ], $yookassaPayment['id'], uniqid('', true));
        } catch (
            BadApiRequestException |
            ForbiddenException |
            InternalServerError |
            NotFoundException |
            ResponseProcessingException |
            TooManyRequestsException |
            UnauthorizedException |
            ApiException |
            ExtensionNotFoundException $e
        ) {
            // Логирование ошибки
            Log::error('Ошибка при попытке захвата платежа', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'payment' => $yookassaPayment,
            ]);

            // Можно вернуть null или специальный объект ошибки
            return null;
        }
    }

    private function updateTicketStatus(Ticket $ticket, string $status, array $requestData): void
    {
        $ticket->status = $status;
        $ticket->refunded_amount = $requestData['object']['amount']['value'] ?? 0;
        $ticket->save();
    }
}
