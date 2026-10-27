<?php

namespace App\Support;

use App\Models\Company;

/** Customer service WhatsApp: the number and wa.me chat links used across the website. */
class WhatsApp
{
    /** From the RENT N GO company record in rngcarrental, else WHATSAPP_NUMBER in .env. */
    public static function number(): ?string
    {
        // Looked up once per request (list pages build a link per card)
        $attributes = request()->attributes;
        if (!$attributes->has('whatsapp_number')) {
            $attributes->set('whatsapp_number', Company::where('name', config('reservation.whatsapp_company'))->value('whatsapp_number')
                ?: config('reservation.whatsapp_number')
                ?: null);
        }

        return $attributes->get('whatsapp_number');
    }

    /**
     * wa.me link with an optional pre-written message. Without a number set up, WhatsApp still opens
     * with the message ready and the customer picks the chat themselves.
     */
    public static function link(?string $message = null): string
    {
        $digits = preg_replace('/\D/', '', (string) self::number());
        // Local numbers (012...) are Malaysian
        if (str_starts_with($digits, '0')) {
            $digits = '6' . $digits;
        }

        return 'https://wa.me/' . $digits . ($message ? '?text=' . rawurlencode($message) : '');
    }
}
