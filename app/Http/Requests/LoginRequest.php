<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string|max:255',
            //           'password' => 'required|min:8'

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
            'required' => 'Поле :attribute не должно быть пустым.',
            'string' => 'Поле :attribute должен быть строкой.',
            'email' => 'Введите действительный :attribute.',
            'min' => [
                'array' => 'Поле :attribute должно содержать не менее :min элементов.',
                'file' => 'Поле :attribute должно быть не менее :min килобайт.',
                'numeric' => 'Поле :attribute должно быть не меньше :min.',
                'string' => 'Поле :attribute должно содержать не менее :min символов.',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => '"Емейл"',
            'password' => '"Пароль"',
        ];
    }
}
