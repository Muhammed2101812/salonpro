<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordRule implements ValidationRule
{
    protected int $minLength = 8;

    protected bool $requireUppercase = true;

    protected bool $requireLowercase = true;

    protected bool $requireNumbers = true;

    protected bool $requireSpecialChars = true;

    public function __construct(
        ?int $minLength = null,
        ?bool $requireUppercase = null,
        ?bool $requireLowercase = null,
        ?bool $requireNumbers = null,
        ?bool $requireSpecialChars = null
    ) {
        $this->minLength = $minLength ?? config('auth.password_policy.min_length', 8);
        $this->requireUppercase = $requireUppercase ?? config('auth.password_policy.require_uppercase', true);
        $this->requireLowercase = $requireLowercase ?? config('auth.password_policy.require_lowercase', true);
        $this->requireNumbers = $requireNumbers ?? config('auth.password_policy.require_numbers', true);
        $this->requireSpecialChars = $requireSpecialChars ?? config('auth.password_policy.require_special_chars', true);
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen($value) < $this->minLength) {
            $fail("Şifre en az {$this->minLength} karakter olmalıdır.");
        }

        if ($this->requireUppercase && ! preg_match('/[A-Z]/', $value)) {
            $fail('Şifre en az bir büyük harf içermelidir.');
        }

        if ($this->requireLowercase && ! preg_match('/[a-z]/', $value)) {
            $fail('Şifre en az bir küçük harf içermelidir.');
        }

        if ($this->requireNumbers && ! preg_match('/[0-9]/', $value)) {
            $fail('Şifre en az bir rakam içermelidir.');
        }

        if ($this->requireSpecialChars && ! preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail('Şifre en az bir özel karakter içermelidir.');
        }
    }
}
