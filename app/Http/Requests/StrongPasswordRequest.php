<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StrongPasswordRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()      // Require at least one uppercase and one lowercase letter
                    ->numbers()        // Require at least one number
                    ->symbols()        // Require at least one symbol
                    ->uncompromised(), // Ensure password hasn't been compromised in data leaks
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.mixed_case' => 'Password harus mengandung huruf besar dan huruf kecil.',
            'password.numbers' => 'Password harus mengandung minimal satu angka.',
            'password.symbols' => 'Password harus mengandung minimal satu simbol (!@#$%^&*).',
            'password.uncompromised' => 'Password ini telah terdeteksi dalam kebocoran data. Silakan gunakan password yang lebih aman.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'password' => 'password',
        ];
    }
}
