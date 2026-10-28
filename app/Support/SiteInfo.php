<?php

namespace App\Support;

use App\Models\Company;

/** Company and contact details for the top bar and footer (company record + config/site.php). */
class SiteInfo
{
    public static function get(): array
    {
        // Built once per request
        $attributes = request()->attributes;
        if ($attributes->has('site_info')) {
            return $attributes->get('site_info');
        }

        $company = Company::where('name', config('reservation.whatsapp_company'))->first();
        $phone = config('site.phone');

        $info = [
            'name'         => 'Rent N Go Car Rental Sdn Bhd',
            'registration' => $company->registration_number ?? null,
            'address'      => self::address($company->full_address ?? null)
                  ?? self::address(config('site.address')),
            'phone'        => $phone,
            'phone_link'   => $phone ? 'tel:' . preg_replace('/[^\d+]/', '', $phone) : null,
            'email'        => config('site.email'),
            'hours'        => config('site.hours'),
            'whatsapp'     => WhatsApp::number(),
            'social'       => array_filter(config('site.social', [])),
        ];
        $attributes->set('site_info', $info);

        return $info;
    }

    /** "2ND FLOOR, SUBLOT 5,\nCHA YI GOLDLAND, ..." -> "2nd Floor, Sublot 5, Cha Yi Goldland, ..." on one line. */
    private static function address(?string $address): ?string
    {
        if (!$address || !trim($address)) {
            return null;
        }

        $text = preg_replace('/\s*[\r\n]+\s*/', ' ', trim($address));
        $text = rtrim(preg_replace('/\s*,\s*/', ', ', $text), ',. ');

        // Only re-case an address typed all in capitals; mixed case stays as staff wrote it
        if ($text === mb_strtoupper($text)) {
            $text = mb_convert_case(mb_strtolower($text), MB_CASE_TITLE);
            $text = preg_replace_callback('/\b(\d+)(St|Nd|Rd|Th)\b/', fn($m) => $m[1] . strtolower($m[2]), $text);
        }

        return $text . '.';
    }
}
