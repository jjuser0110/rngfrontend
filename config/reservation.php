<?php

// Keep these in line with rngcarrental's config/reservation.php: both apps write the same reservations.

return [

    /*
    |--------------------------------------------------------------------------
    | Security Deposit
    |--------------------------------------------------------------------------
    |
    | Default security deposit (RM) put on new reservations. Staff can still
    | change it per reservation in rngcarrental step 3.
    |
    */

    'security_deposit' => env('RESERVATION_SECURITY_DEPOSIT', 200),

    /*
    |--------------------------------------------------------------------------
    | Customer Service WhatsApp
    |--------------------------------------------------------------------------
    |
    | Shown to customers after they send a booking request. The number comes
    | from this company's "Customer Service WhatsApp Number" in rngcarrental
    | (Companies); WHATSAPP_NUMBER is only used while that is empty.
    |
    */

    'whatsapp_company' => 'RENT N GO CAR RENTAL SDN BHD',

    'whatsapp_number' => env('WHATSAPP_NUMBER'),

];
