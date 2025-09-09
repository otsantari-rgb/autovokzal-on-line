<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:App\Models\User,email',
            'password' => 'required|string|confirmed|max:255',
            //            'password' => 'required|string|confirmed|min:8|regex:/[0-9]/|regex:/[@$!%*?&#.,]/',
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
            // Для имени
            'name.required' => 'Пожалуйста, введите ваше имя.',
            'name.string' => 'Имя должно быть строкой.',
            'name.max' => 'Имя не может содержать более 255 символов.',

            // Для email
            'email.required' => 'Пожалуйста, введите ваш адрес электронной почты.',
            'email.email' => 'Укажите действительный адрес электронной почты.',
            'email.max' => 'Адрес электронной почты не может содержать более 255 символов.',
            'email.unique' => 'Этот адрес электронной почты уже зарегистрирован.',

            // Для пароля
            'password.required' => 'Пожалуйста, введите пароль.',
            'password.confirmed' => 'Пароли должны совпадать.',
            'password.min' => 'Пароль должен содержать не менее 8 символов.',
            'password.regex' => 'Пароль должен содержать хотя бы одну цифру и один специальный символ (@, $, !, %, *, ?, &, #, ., ,,).',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '"Имя"',
            'email' => '"Емейл"',
            'password' => '"Пароль"',
        ];
    }
}
