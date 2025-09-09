<?php

namespace App\Services\Atol;

class AtolReceipt
{
    private array $items;
    private float $totalSum;

    public function __construct(array $items = [], float $totalSum = 0) {
        $this->items = $items;
        $this->totalSum = $totalSum;
    }

    public function addItem(string $name, float $sum, string $paymentMethod = 'full_payment', string $paymentObject = 'commodity', string $vatType = 'vat0'): self
    {
        $this->items[] = [
            'name' => $name,
            'price' => $sum,
            'quantity' => 1,
            'sum' => $sum,
            'payment_method' => $paymentMethod,
            'payment_object' => $paymentObject,
            'vat' => [
                'type' => $vatType,
                'sum' => 0.00
            ]
        ];

        $this->totalSum += $sum;

        return $this;
    }

    public function getData(string $externalId, string $operationType, string $email): array
    {
        return [
            'external_id' => $externalId . '-' . $operationType,
            'receipt' => [
                'client' => ['email' => $email],
                'company' => [
                    'email' => config('mail.from.address'),
                    'sno' => 'usn_income',
                    'inn' => config('services.biletavto.inn'),
                    'payment_address' => config('app.url'),
                ],
                'items' => $this->items,
                'payments' => [
                    [
                        'type' => 1,
                        'sum' => $this->totalSum
                    ]
                ],
                'vats' => [
                    [
                        'type' => 'vat0',
                        'sum' => 0.00
                    ]
                ],
                'total' => $this->totalSum
            ],
            'service' => ['callback_url' => config('app.url') . '/api/receipt-callback'],
            'timestamp' => now()->format('d.m.Y H:i:s')
        ];
    }
}
