<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefundRequest extends FormRequest
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
            'id' => 'required|integer',
            'username' => 'required|string|max:100',
            'refund_amount' => 'required|numeric|min:0',
            'comment' => 'nullable|string|max:255',
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
            'username.required' => 'Пожалуйста, введите ваше имя, чтобы знать кто накосячил.',
            'username.max' => 'Имя не может содержать более 100 символов.',

            'refund_amount.required' => 'Пожалуйста, введите сумму возврата.',
            'refund_amount.numeric' => 'Сумма возврата не должна содержать посторонние символы, кроме цифр.',
            'refund_amount.min' => 'Сумма возврата не должна быть отрицательной.',

            'comment.max' => 'Комментарий не должен превышать 255 символов.',

            'id.required' => 'Пожалуйста, введите ID билета.',
            'id.integer' => 'ID билета должен быть числом'
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => '"ID билета"',
            'username' => '"Имя администратора"',
            'refund_amount' => '"Сумма возврата"',
            'comment' => '"Комментарий"',
        ];
    }
}
