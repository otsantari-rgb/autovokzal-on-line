<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'patronymic' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'type' => 'required|in:passport,birth_certificate',
            'number' => 'required|string|max:255',
            'birthday' => 'required|date|before_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'before_or_equal' => [
                'today' => 'Поле :attribute должно быть датой не позднее сегодняшнего дня.'
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'firstname' => '"Имя"',
            'lastname' => '"Фамилия"',
            'patronymic' => '"Отчество"',
            'gender' => '"Пол"',
            'type' => '"Тип документа"',
            'number' => '"Номер документа"',
            'birthday' => '"Дата рождения"',
            'today' => '"сегодня"',
        ];
    }
}
