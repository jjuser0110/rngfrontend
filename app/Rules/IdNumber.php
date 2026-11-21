<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Customer IC / passport number, checked by its type.
 * IC: Malaysian MyKad, YYMMDD-PB-#### (e.g. 201003-13-5111), with a real birth date.
 * Passport: 6-20 letters and digits.
 * Keep in step with rngcarrental's App\Rules\IdNumber.
 */
class IdNumber implements ValidationRule
{
    public const TYPES = ['ic' => 'IC (MyKad)', 'passport' => 'Passport'];

    public function __construct(private ?string $type)
    {
    }

    /** ICs are saved with dashes (201003-13-5111); passports in capitals without spaces. */
    public static function normalize(?string $type, ?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        if ($type === 'ic') {
            $digits = preg_replace('/\D/', '', $value);

            return strlen($digits) === 12
                ? substr($digits, 0, 6) . '-' . substr($digits, 6, 2) . '-' . substr($digits, 8)
                : trim($value);
        }

        return strtoupper(preg_replace('/\s+/', '', $value));
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = self::normalize($this->type, (string) $value);

        if ($this->type === 'passport') {
            if (!preg_match('/^[A-Z0-9]{6,20}$/', $value)) {
                $fail('Please enter a valid passport number (letters and numbers only).');
            }

            return;
        }

        if (!preg_match('/^(\d{2})(\d{2})(\d{2})-\d{2}-\d{4}$/', $value, $parts)) {
            $fail('Please enter the IC number as YYMMDD-PB-#### (e.g. 201003-13-5111).');

            return;
        }

        [, $year, $month, $day] = $parts;
        if (!checkdate((int) $month, (int) $day, 2000 + (int) $year) && !checkdate((int) $month, (int) $day, 1900 + (int) $year)) {
            $fail('The first 6 digits of the IC must be a real date of birth (YYMMDD).');
        }
    }
}
