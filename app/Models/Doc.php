<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doc extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'patronymic',
        'gender',
        'type',
        'number',
        'birthday',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTranslatedGender(): string
    {
        $statusTranslations = [
            'male' => 'Мужской',
            'female' => 'Женский',
        ];

        return $statusTranslations[$this->gender];
    }

    public function getTranslatedType(): string
    {
        $statusTranslations = [
            'passport' => 'Паспорт',
            'birth_certificate' => 'Свидетельство о рождении',
        ];

        return $statusTranslations[$this->type];
    }
}
