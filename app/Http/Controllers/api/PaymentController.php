<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\YookassaWebhookRequest;
use App\Models\Order;
use App\Services\PaymentEventHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
use Throwable;

class PaymentController extends Controller
{
    protected PaymentEventHandler $eventHandler;

    protected Client $client;

    public function __construct(PaymentEventHandler $eventHandler, Client $client)
    {
        $this->eventHandler = $eventHandler;
        $this->client = $client;
    }

    public function callback(YookassaWebhookRequest $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (! isset($requestData['event'])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
        }

        $eventType = $requestData['event'];

        try {
            return match ($eventType) {
                'payment.succeeded' => $this->eventHandler->handlePaymentSucceeded($requestData),
                'payment.waiting_for_capture' => $this->eventHandler->handleWaitingForCapture($requestData),
                'payment.canceled' => $this->eventHandler->handlePaymentCanceled($requestData),
                'refund.succeeded' => $this->eventHandler->handleRefundSucceeded($requestData),
                default => $this->handleUnknownEvent($eventType, $requestData),
            };
        } catch (Throwable $e) {
            Log::error('Error handling payment event', [
                'event' => $eventType,
                'error' => $e->getMessage(),
                'payload' => $requestData,
            ]);

            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    protected function handleUnknownEvent(string $eventType, array $requestData): JsonResponse
    {
        Log::warning('Unknown event type received', ['event' => $eventType, 'payload' => $requestData]);

        return response()->json(['status' => 'error', 'message' => 'Unknown event type'], 400);
    }

    /**
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws ApiException
     * @throws ExtensionNotFoundException
     * @throws BadApiRequestException
     * @throws InternalServerError
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    public function status($uuid): JsonResponse
    {
        $order = Order::where('uuid', $uuid)->firstOrFail();

        $yookassaPayment = $this->client->getPaymentInfo($order->payment->yookassa_id);
        if ($yookassaPayment->status === 'pending') {
            $confirmationUrl = $yookassaPayment->getConfirmation()->getConfirmationUrl();
        }
        $payment = $order->payment;
        $payment->status = $yookassaPayment->getStatus();
        $payment->save();

        return response()->json([
            'status' => $payment->status,
            'email' => $order->user->email,
            'payment_url' => $confirmationUrl ?? '',
            'translated_status' => $payment->getTranslatedStatus(),
        ]);
    }
}
