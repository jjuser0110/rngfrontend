<?php

namespace App\Support;

/** Homestay and tour prices: whole ringgit without decimals (RM 1,280), with sen when there are any (RM 149.50). */
class Price
{
    public static function format(float|int|string|null $amount): string
    {
        $amount = round((float) $amount, 2);

        return number_format($amount, fmod($amount, 1) == 0 ? 0 : 2);
    }
}
