<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TestRequest extends FormRequest
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
            'date' => '|date|after_or_equal:today',
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
            'date.date' => 'Поле "Дата" должно быть корректной датой.',
            'date.after_or_equal' => 'Поле "Дата" не может быть прошедшей.',
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('to')) {
            $this->merge([
                'to' => mb_convert_case(mb_strtolower($this->to), MB_CASE_TITLE),
            ]);
        }

        if ($this->has('date')) {
            $timestamp = strtotime($this->date);
            $formattedDate = $timestamp !== false ? date('d.m.Y', $timestamp) : null;
            $this->merge(['date' => $formattedDate]);
        }
    }
}
