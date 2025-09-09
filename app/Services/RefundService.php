<?php

namespace App\Services;

use App\Clients\BiletAvtoApiClient;
use App\Jobs\SendRefundedTicketToAdminMailJob;
use App\Jobs\SendRefundMailJob;
use App\Models\Ticket;
use App\Traits\TimeFormatter;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use YooKassa\Client;
use YooKassa\Common\Exceptions\ApiConnectionException;
use YooKassa\Common\Exceptions\ApiException;
use YooKassa\Common\Exceptions\AuthorizeException;
use YooKassa\Common\Exceptions\BadApiRequestException;
use YooKassa\Common\Exceptions\ExtensionNotFoundException;
use YooKassa\Common\Exceptions\ForbiddenException;
use YooKassa\Common\Exceptions\InternalServerError;
use YooKassa\Common\Exceptions\NotFoundException;
use YooKassa\Common\Exceptions\ResponseProcessingException;
use YooKassa\Common\Exceptions\TooManyRequestsException;
use YooKassa\Common\Exceptions\UnauthorizedException;
use YooKassa\Model\CurrencyCode;
use YooKassa\Request\Refunds\CreateRefundRequest;

class RefundService
{
    use TimeFormatter;

    protected BiletAvtoApiClient $apiClient;

    private Client $client;

    public function __construct(BiletAvtoApiClient $apiClient, Client $client)
    {
        $this->apiClient = $apiClient;
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function refundTicket(Ticket $ticket, string $role, string $type, $refundAmount = null, $comment = ''): array
    {
        $currentDateTime = now();
        $departureTime = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $ticket->departure_date . ' ' . $ticket->departure_time
        );

        // Проверка на время отправления и время до отправления
        if ($currentDateTime->greaterThanOrEqualTo($departureTime)) {
            return [
                'success' => false,
                'message' => 'После отправления автобуса возврат билета невозможен.',
            ];
        }

        $differenceInMinutes = $departureTime->diffInMinutes($currentDateTime, true);

        if ($differenceInMinutes < 30) {
            return [
                'success' => false,
                'message' => 'Менее, чем за полчаса до отправления автобуса возврат билета оформляется только через'
                    . 'подачу письменного заявления на кассах автовокзала.',
            ];
        }

        $routeRefundLimit = config("refund_route_time_rules.$ticket->route_name");
        if ($routeRefundLimit !== null) {
            if ($differenceInMinutes < $routeRefundLimit) {
                $timeLimitMessage = $this->getTimeWord($routeRefundLimit);

                return [
                    'success' => false,
                    'message' => "Оформить возврат билета возможно не позднее, чем за $timeLimitMessage до отправления.",
                ];
            }
        }

        try {
            $refundAmount = $this->calculateRefundAmount($ticket);
            $this->apiClient->refund($ticket->ba_ticket_id);
            SendRefundMailJob::dispatch($ticket, $role, $comment, $refundAmount)->onQueue('refund');

            $ticket->status = 'pending';
            $ticket->save();

            return [
                'success' => true,
                'message' => 'Заявка на возврат успешно оформлена!',
                'data' => [
                    'refund_amount' => $refundAmount,
                    'ticket' => $ticket,
                ],
            ];
        } catch (Exception $e) {
            Log::critical('Неожиданная ошибка при возврате билета', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ошибка при возврате. Попробуйте позже.',
            ];
        }
    }

    public function refundAdmin(Ticket $ticket, int|float $refundAmount = null, string $comment = '', string $username = ''): array
    {
        try {
            $this->processYooKassaRefund($ticket, $refundAmount, $comment);
//            $this->apiClient->refund($ticket->ba_ticket_id);
            SendRefundedTicketToAdminMailJob::dispatch($ticket, $comment, $refundAmount, $username)->onQueue('refunded');
            return [
                'success' => true,
                'message' => 'Возврат успешно осуществлён!',
                'data' => [
                    'refund_amount' => $refundAmount,
                    'ticket' => $ticket,
                ],
            ];
        } catch (Exception $e) {
            Log::critical('Неожиданная ошибка при возврате платежа', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ошибка при возврате. Попробуйте позже.',
                'data' =>  $e->getMessage(),
            ];
        }
    }

    public function calculateRefundAmount(Ticket $ticket): float
    {
        $currentDateTime = now();
        $departureTime = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $ticket->departure_date . ' ' . $ticket->departure_time
        );
        $refund_amount = $ticket->nominal;
        $differenceInHours = $departureTime->diffInHours($currentDateTime, true);

        if ($differenceInHours < 2) {
            $refund_amount -= $ticket->nominal * 0.15; // Удерживаем 15%
        } else {
            $refund_amount -= $ticket->nominal * 0.05; // Удерживаем 5%
        }

        return max(0, $refund_amount); // Если сумма меньше нуля, устанавливаем 0
    }

    /**
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws AuthorizeException
     * @throws InternalServerError
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws ApiConnectionException
     * @throws UnauthorizedException
     */
    private function processYooKassaRefund(Ticket $ticket, float $refund_amount, string $comment): void
    {
        $paymentId = $ticket->order->payment->yookassa_id;

        $refundBuilder = CreateRefundRequest::builder();
        $refundBuilder
            ->setPaymentId($paymentId)
            ->setAmount($refund_amount)
            ->setCurrency(CurrencyCode::RUB)
            ->setDescription('Возврат билета №' . $ticket->id . ' ' . $comment)
            ->setReceiptItems([
                [
                    'description' => 'Билет №' . $ticket->id,
                    'quantity' => '1.00',
                    'amount' => [
                        'value' => $refund_amount,
                        'currency' => CurrencyCode::RUB,
                    ],
                    'vat_code' => 2,
                    'payment_mode' => 'full_payment',
                    'payment_subject' => 'commodity',
                ],
            ])
            ->setReceiptEmail($ticket->user->email)
            ->setTaxSystemCode(1);

        $request = $refundBuilder->build();
        $idempotenceKey = uniqid('', true);
        $response = $this->client->createRefund($request, $idempotenceKey);
        Log::info(json_encode($response));
    }
}
