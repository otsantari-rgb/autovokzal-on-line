<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmBookingRequest extends FormRequest
{
    /**
     * Определите, авторизован ли пользователь для выполнения этого запроса.
     */
    public function authorize(): bool
    {
        return true; // Измените логику, если требуется авторизация
    }

    /**
     * Получите правила валидации для запроса.
     */
    public function rules(): array
    {
        return [
            'sheet_id' => 'required|integer',
            'price_id' => 'required|integer',
            'total_price' => 'required|numeric',
            'selected_seats' => 'required|array',
            'selected_seats.*' => 'integer',
            'passenger_data' => 'nullable|array',
            'passenger_data.*.place' => 'nullable|integer',
            'passenger_data.*.lastname' => 'nullable|string',
            'passenger_data.*.firstname' => 'nullable|string',
            'passenger_data.*.patronymic' => 'nullable|string',
            'passenger_data.*.gender' => 'nullable|in:male,female',
            'passenger_data.*.birthday' => 'nullable|date',
            'passenger_data.*.document_type' => 'nullable|in:passport,birth_certificate',
            'passenger_data.*.docs_number' => 'nullable|string',
            'passenger_data.*.tariff_type' => 'nullable|string',
            'passenger_data.*.no_patronymic' => 'nullable|boolean',
            'contact_info' => 'nullable|array',
            'contact_info.email' => 'nullable|email',
            'contact_info.phone' => 'nullable|regex:/^(\+?[0-9]{1,3})?(\(?\d{1,4}\)?)?[\d\- ]{5,15}$/',
        ];
    }

    /**
     * Настройка пользовательских сообщений для ошибок валидации.
     */
    public function messages(): array
    {
        return [
            'required' => 'Необходимо указать :attribute.',
            'integer' => ':attribute должен быть целым числом.',
            'numeric' => ':attribute должен быть числом.',
            'string' => ':attribute должен быть строкой.',
            'array' => ':attribute должен быть массивом.',
            'date' => ':attribute должен быть корректной датой.',
            'email' => ':attribute должен быть корректным email адресом.',
            'in' => 'Выбранное значение для :attribute некорректно.',
            'boolean' => ':attribute должен быть логическим значением.',
            'min' => [
                'numeric' => ':attribute не может быть меньше :min.',
                'file' => ':attribute не может быть меньше :min килобайт.',
                'string' => ':attribute не может быть короче :min символов.',
                'array' => ':attribute должен содержать не менее :min элементов.',
            ],
            'before' => ':attribute должна быть датой до :date.',
            'passenger_data.*.birthday.before' => 'Дата рождения должна быть раньше сегодняшнего дня.',
            'regex' => ':attribute имеет некорректный формат.',
            'contact_info.phone.regex' => 'Номер телефона должен быть в формате +X (XXX) XXX-XX-XX или подобном.',
        ];
    }

    /**
     * Настройка пользовательских имен полей.
     */
    public function attributes(): array
    {
        return [
            'sheet_id' => 'Идентификатор рейса в БилетАвто',
            'price_id' => 'Идентификатор цены в БилетАвто',
            'total_price' => 'Общая стоимость',
            'selected_seats' => 'Выбранные места',
            'selected_seats.*' => 'Выбранное место',
            'passenger_data' => 'Данные пассажиров',
            'passenger_data.*.place' => 'Место пассажира',
            'passenger_data.*.lastname' => 'Фамилия пассажира',
            'passenger_data.*.firstname' => 'Имя пассажира',
            'passenger_data.*.patronymic' => 'Отчество пассажира',
            'passenger_data.*.gender' => 'Пол пассажира',
            'passenger_data.*.birthday' => 'Дата рождения пассажира',
            'passenger_data.*.document_type' => 'Тип документа',
            'passenger_data.*.docs_number' => 'Номер документа',
            'passenger_data.*.tariff_type' => 'Тип тарифа',
            'passenger_data.*.no_patronymic' => 'Отсутствие отчества',
            'contact_info.email' => 'Электронная почта',
            'contact_info.phone' => 'Телефон',
        ];
    }
}
