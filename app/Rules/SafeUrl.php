<?php

namespace App\Rules;

use App\Support\SiteUrl;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (SiteUrl::href((string) $value) === null) {
            $fail('Укажите ссылку, которая начинается с /, #, http://, https://, mailto: или tel:.');
        }
    }
}
