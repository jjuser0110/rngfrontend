<?php

// Contact details shown in the top bar, footer and Contact Us page. Hardcoded for now (not .env).
// The company name, registration number and address come from the RENT N GO company record in
// rngcarrental (reservation.whatsapp_company). Anything left as null is simply not shown.

return [

    // e.g. '+60 82-123 456'
    'phone' => '+60182285566',

    // e.g. 'info@rentngo.com.my'
    'email' => 'rentngocarrentalsdnbhd@gmail.com',

    // e.g. 'Mon - Sun 08:00 - 20:00'
    'hours' => 'Mon - Sun 08:00 - 20:00',

    // Full profile URLs, e.g. 'https://www.facebook.com/rentngo'
    'social' => [
        'facebook' => 'https://www.facebook.com/rentngocarrentalsdnbhd',
        'instagram' => 'https://www.instagram.com/rngcarrental',
        'youtube'   => null,
    ],

];
