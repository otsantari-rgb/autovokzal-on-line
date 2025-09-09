<?php

namespace App\Services;

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

class PaymentService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws NotFoundException
     * @throws ApiException
     * @throws ResponseProcessingException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws AuthorizeException
     * @throws InternalServerError
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws ApiConnectionException
     * @throws UnauthorizedException
     */
    public function createPayment($order, $payment): string
    {
        $ticketsInfo = [];
        foreach ($order->tickets()->get() as $ticket) {
            $ticketsInfo[] = [
                'description' => 'Билет №' . $ticket->id,
                'quantity' => '1.00',
                'amount' => [
                    'value' => $ticket->price,
                    'currency' => 'RUB',
                ],
                'vat_code' => '2',
                'payment_mode' => 'full_payment',
                'payment_subject' => 'commodity',
            ];
        }

        // Создаем объект запроса
        $request = [
            'amount' => [
                'value' => $payment->amount,
                'currency' => 'RUB',
            ],
            "payment_method_data" => [
                "type" => $payment->method,
            ],
            'confirmation' => [
                'type' => 'redirect',
                'locale' => 'ru_RU',
                'return_url' => config('app.url') . '/payment-info/' . $order->uuid,
            ],
            'capture' => $payment->method === 'sbp',
            'description' => 'Оплата заказа на сайте autovokzal-on-line.ru. Заказ № ' . $order->id,
            'metadata' => [
                'order_id' => $order->id,
                'language' => 'ru',
                'transaction_id' => $payment->id,
            ],
            'receipt' => [
                'customer' => [
                    'email' => $order->user->email,
                    'phone' => $order->user->phone,
                ],
                'items' => $ticketsInfo,
            ],
        ];
        $idempotenceKey = uniqid('', true);
        $response = $this->client->createPayment($request, $idempotenceKey);

        $confirmationUrl = $response->getConfirmation()->getConfirmationUrl();
        $payment->url = $confirmationUrl;
        $payment->yookassa_id = $response->getId();
        $payment->save();

        return $confirmationUrl;
    }
}
