<?php

namespace App\Rules;

use Illuminate\Validation\Rules\Password;

class PasswordRules
{
    /**
     * Get standard password rules for the application
     *
     * @param bool $requireConfirmation
     * @return array
     */
    public static function standard(bool $requireConfirmation = true): array
    {
        $rules = [
            'required',
            'string',
            Password::min(8)
                ->mixedCase()      // Require at least one uppercase and one lowercase letter
                ->numbers()        // Require at least one number
                ->symbols()        // Require at least one symbol
                ->uncompromised(), // Ensure password hasn't been compromised in data leaks
        ];

        if ($requireConfirmation) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }

    /**
     * Get admin password rules (stricter requirements)
     *
     * @param bool $requireConfirmation
     * @return array
     */
    public static function admin(bool $requireConfirmation = true): array
    {
        $rules = [
            'required',
            'string',
            Password::min(12)              // Longer minimum for admins
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()
                ->rules(['regex:/^(?=.*[!@#$%^&*])/']) // At least one special character
        ];

        if ($requireConfirmation) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }

    /**
     * Get password rules for sensitive operations (even stricter)
     *
     * @param bool $requireConfirmation
     * @return array
     */
    public static function sensitive(bool $requireConfirmation = true): array
    {
        $rules = [
            'required',
            'string',
            Password::min(14)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()
                ->rules([
                    'regex:/^(?=.*[A-Z])/',       // At least one uppercase
                    'regex:/^(?=.*[a-z])/',       // At least one lowercase
                    'regex:/^(?=.*[0-9])/',       // At least one number
                    'regex:/^(?=.*[!@#$%^&*])/',  // At least one special character
                ])
        ];

        if ($requireConfirmation) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }

    /**
     * Get custom error messages for password validation
     *
     * @return array
     */
    public static function messages(): array
    {
        return [
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal :min karakter.',
            'password.mixed_case' => 'Password harus mengandung huruf besar dan huruf kecil.',
            'password.numbers' => 'Password harus mengandung minimal satu angka.',
            'password.symbols' => 'Password harus mengandung minimal satu simbol (!@#$%^&*).',
            'password.uncompromised' => 'Password ini telah terdeteksi dalam kebocoran data. Silakan gunakan password yang lebih aman.',
            'password.regex' => 'Password tidak memenuhi kriteria keamanan yang diperlukan.',
        ];
    }

    /**
     * Get password strength indicator for UI
     *
     * @param string $password
     * @return array ['strength' => 'weak|medium|strong|very_strong', 'score' => int]
     */
    public static function checkStrength(string $password): array
    {
        $score = 0;
        $length = strlen($password);

        // Length score
        if ($length >= 8) $score += 1;
        if ($length >= 12) $score += 1;
        if ($length >= 16) $score += 1;

        // Character variety score
        if (preg_match('/[a-z]/', $password)) $score += 1; // lowercase
        if (preg_match('/[A-Z]/', $password)) $score += 1; // uppercase
        if (preg_match('/[0-9]/', $password)) $score += 1; // numbers
        if (preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) $score += 1; // symbols

        // Determine strength
        $strength = 'weak';
        if ($score >= 7) {
            $strength = 'very_strong';
        } elseif ($score >= 5) {
            $strength = 'strong';
        } elseif ($score >= 3) {
            $strength = 'medium';
        }

        return [
            'strength' => $strength,
            'score' => $score,
            'max_score' => 10,
        ];
    }
}
