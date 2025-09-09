<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Убедитесь, что доступ разрешён
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $userId = Auth::id(); // Получить текущего пользователя

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
        ];

        // Если передан новый пароль, проверяем его и текущий пароль
        if ($this->filled('new_password')) {
            $rules['current_password'] = 'required|string'; // Проверка текущего пароля
            $rules['new_password'] = 'required|string|confirmed'; // Новый пароль (необходим)
        }

        return $rules;
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Поле "Имя" обязательно для заполнения.',
            'name.string' => 'Поле "Имя" должно быть строкой.',
            'name.max' => 'Поле "Имя" не может содержать более 255 символов.',

            'email.required' => 'Поле "Электронная почта" обязательно для заполнения.',
            'email.email' => 'Введите действительный адрес электронной почты.',
            'email.max' => 'Поле "Электронная почта" не может содержать более 255 символов.',
            'email.unique' => 'Этот адрес электронной почты уже используется.',

            'current_password.required' => 'Поле "Текущий пароль" обязательно для заполнения.',
            'current_password.string' => 'Поле "Текущий пароль" должно быть строкой.',
            'current_password.min' => 'Текущий пароль должен быть не менее 8 символов.',

            'new_password.required' => 'Поле "Новый пароль" обязательно для заполнения.',
            'new_password.string' => 'Поле "Новый пароль" должно быть строкой.',
            'new_password.min' => 'Новый пароль должен быть не менее 8 символов.',
            'new_password.confirmed' => 'Пароли не совпадают.',
        ];
    }

    /**
     * Validate the current password.
     */
    public function validateCurrentPassword(): bool
    {
        $user = Auth::user();

        return Hash::check($this->input('current_password'), $user->password);
    }
}
