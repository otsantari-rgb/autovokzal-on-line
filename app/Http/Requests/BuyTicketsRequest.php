<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BuyTicketsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            '_token' => 'required',
            'sheet_id' => 'required|integer',
            'price_id' => 'required|integer',
            'total_price' => 'required|numeric',
            'selected_seats' => 'required|array',
            'selected_seats.*' => 'integer',
            'email' => 'required|email',
            'phone' => 'required|regex:/^(\+?[0-9]{1,3})?(\(?\d{1,4}\)?)?[\d\- ]{5,15}$/',
            'passenger' => 'required|array',
            'passenger.*.place' => 'required|integer',
            'passenger.*.firstname' => 'required|string|max:255',
            'passenger.*.name' => 'required|string|max:255',
            'passenger.*.lastname' => 'nullable|string|max:255',
            'passenger.*.gender' => 'required|in:male,female',
            'passenger.*.birthday' => 'required|date_format:Y-m-d',
            'passenger.*.document_type' => 'required|string',
            'passenger.*.docs_number' => 'required|string',
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Общие поля
            '_token.required' => 'Не удалось подтвердить запрос. Попробуйте ещё раз.',
            'sheet_id.required' => 'Необходимо указать ID листа.',
            'sheet_id.integer' => 'ID листа должен быть числом.',
            'price_id.required' => 'Необходимо указать ID цены.',
            'price_id.integer' => 'ID цены должен быть числом.',
            'total_price.required' => 'Необходимо указать общую стоимость.',
            'total_price.numeric' => 'Общая стоимость должна быть числом.',
            'selected_seats.required' => 'Необходимо выбрать места.',
            'selected_seats.array' => 'Поле "выбранные места" должно быть массивом.',
            'selected_seats.*.integer' => 'Каждое выбранное место должно быть числом.',
            'email.required' => 'Пожалуйста, введите адрес электронной почты.',
            'email.email' => 'Укажите действительный адрес электронной почты.',
            'phone.required' => 'Пожалуйста, введите номер телефона.',
            'phone.regex' => 'Укажите действительный номер телефона.',

            // Пассажиры
            'passenger.required' => 'Необходимо указать информацию о пассажирах.',
            'passenger.array' => 'Поле пассажиров должно быть массивом.',
            'passenger.*.place.required' => 'Укажите место пассажира.',
            'passenger.*.place.integer' => 'Место пассажира должно быть числом.',
            'passenger.*.firstname.required' => 'Введите имя пассажира.',
            'passenger.*.firstname.string' => 'Имя пассажира должно быть строкой.',
            'passenger.*.firstname.max' => 'Имя пассажира не может превышать 255 символов.',
            'passenger.*.name.required' => 'Введите отчество пассажира.',
            'passenger.*.name.string' => 'Отчество пассажира должно быть строкой.',
            'passenger.*.name.max' => 'Отчество пассажира не может превышать 255 символов.',
            'passenger.*.lastname.string' => 'Фамилия пассажира должна быть строкой.',
            'passenger.*.lastname.max' => 'Фамилия пассажира не может превышать 255 символов.',
            'passenger.*.gender.required' => 'Укажите пол пассажира.',
            'passenger.*.gender.in' => 'Пол пассажира должен быть "male" или "female".',
            'passenger.*.birthday.required' => 'Укажите дату рождения пассажира.',
            'passenger.*.birthday.date_format' => 'Дата рождения должна быть в формате ГГГГ-ММ-ДД.',
            'passenger.*.document_type.required' => 'Укажите тип документа пассажира.',
            'passenger.*.document_type.string' => 'Тип документа пассажира должен быть строкой.',
            'passenger.*.docs_number.required' => 'Укажите номер документа пассажира.',
            'passenger.*.docs_number.string' => 'Номер документа пассажира должен быть строкой.',
        ];
    }
}
