<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingDataRequest extends FormRequest
{
    /**
     * Определите, авторизован ли пользователь выполнять этот запрос.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации для запроса.
     */
    public function rules(): array
    {
        return [
            'sheet' => 'required|array',
            'sheet.id' => 'required|integer',
            'sheet.name' => 'required|string',
            'sheet.departure_station' => 'required|string',
            'sheet.departure_date' => 'required|date_format:d.m.Y',
            'sheet.departure_time' => 'required|date_format:H:i',
            'sheet.departure_address' => 'required|string',
            'sheet.arrival_station' => 'required|string',
            'sheet.arrival_date' => 'required|date_format:d.m.Y',
            'sheet.arrival_time' => 'required|date_format:H:i',
            'sheet.arrival_address' => 'required|string',
            'sheet.status' => 'nullable|string',
            'sheet.freePlaces' => 'required|integer|min:0',
            'sheet.carrier' => 'required|string',
            'sheet.carrier_tin' => 'nullable|string',
            'sheet.price' => 'required|numeric|min:0',
            'sheet.price_id' => 'required|integer',
        ];
    }

    /**
     * Сообщения об ошибках валидации.
     */
    public function messages(): array
    {
        return [
            'required' => 'Поле :attribute обязательно для заполнения.',
            'integer' => 'Поле :attribute должно быть целым числом.',
            'numeric' => 'Поле :attribute должно быть числом.',
            'string' => 'Поле :attribute должно быть строкой.',
            'array' => 'Поле :attribute должно быть массивом.',
            'date_format' => 'Поле :attribute должно быть в формате :format.',
            'min' => 'Поле :attribute не может быть меньше :min.',
        ];
    }

    /**
     * Маппинг атрибутов на читаемые имена.
     */
    public function attributes(): array
    {
        return [
            'sheet' => 'данные рейса',
            'sheet.id' => 'ID рейса',
            'sheet.name' => 'название рейса',
            'sheet.departure_station' => 'станция отправления',
            'sheet.departure_date' => 'дата отправления',
            'sheet.departure_time' => 'время отправления',
            'sheet.departure_address' => 'адрес отправления',
            'sheet.arrival_station' => 'станция прибытия',
            'sheet.arrival_date' => 'дата прибытия',
            'sheet.arrival_time' => 'время прибытия',
            'sheet.arrival_address' => 'адрес прибытия',
            'sheet.status' => 'статус рейса',
            'sheet.freePlaces' => 'количество свободных мест',
            'sheet.carrier' => 'перевозчик',
            'sheet.carrier_tin' => 'ИНН перевозчика',
            'sheet.price' => 'цена',
            'sheet.price_id' => 'ID цены',
        ];
    }
}
