<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'ride_id',
        'price_id',
        'ba_ticket_id',
        'user_id',
        'passenger_phone',
        'passenger_name',
        'passenger_gender',
        'passenger_docs_type',
        'passenger_docs_number',
        'place',
        'price',
        'nominal',
        'departure_station',
        'departure_date',
        'departure_time',
        'departure_address',
        'arrival_station',
        'arrival_date',
        'arrival_time',
        'arrival_address',
        'route_name',
        'type',
        'order_id',
        'ticket_url',
        'status',
        'refunded_amount',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getTranslatedStatus(): string
    {
        $statusTranslations = [
            'pending' => 'Возврат',
            'booked' => 'Забронирован',
            'confirmed' => 'Подтвержден',
            'canceled' => 'Отменен',
            'refunded' => 'Возвращен',
        ];

        return $statusTranslations[$this->status];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->uuid)) {
                $ticket->uuid = Str::uuid();  // Генерация UUID, если оно не установлено
            }
        });
    }
}
