<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || trim((string) $value) === '') {
            return;
        }

        $value = trim((string) $value);

        if (preg_match('/^[+]?[\d\s\-().]+$/u', $value) !== 1) {
            $fail($this->message());

            return;
        }

        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if (strlen($digits) < 10 || strlen($digits) > 15) {
            $fail($this->message());
        }
    }

    private function message(): string
    {
        return 'Укажите телефон в формате +7 (988) 096-20-14, +79880962014, 8 (988) 096-20-14 или +7 988 096-20-14.';
    }
}
