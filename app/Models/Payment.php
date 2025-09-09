<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'method',
        'status',
        'yookassa_id',
        'url',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getTranslatedStatus(): string
    {
        $statusTranslations = [
            'pending' => 'В процессе оплаты',
            'waiting_for_capture' => 'Ожидание подтверждения',
            'succeeded' => 'Успешная оплата',
            'canceled' => 'Отмена платежа',
        ];

        return $statusTranslations[$this->status];
    }
}
