<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * International phone number in E.164 format, e.g. +60123456789.
 * The browser's country-code picker always submits this format.
 */
class PhoneNumber implements ValidationRule
{
    /** Strip spaces, dashes and brackets so numbers are stored as +60123456789. */
    public static function normalize(?string $value): string
    {
        return preg_replace('/[\s()-]/', '', (string) $value);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $number = self::normalize($value);

        if (!preg_match('/^\+[1-9][0-9]{6,14}$/', $number)) {
            $fail('Please enter a valid phone number with its country code.');
            return;
        }

        // Malaysia: keep the stricter check the old form used
        if (str_starts_with($number, '+60') && !preg_match('/^\+60[1-9][0-9]{7,9}$/', $number)) {
            $fail('Invalid Malaysian phone number.');
        }
    }
}
