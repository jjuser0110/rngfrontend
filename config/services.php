<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Places autocomplete for pickup / return addresses (same key as rngcarrental)
    'google_maps' => [
        'key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    // DOKU Checkout: customers pay the security deposit online (App\Payments\Doku).
    // Client ID and Secret Key come from the DOKU Back Office. The Secret Key stays in .env only.
    'doku' => [
        // Off until the keys are set and testing is done: no "Pay deposit" button, payment pages give 404
        'enabled'        => (bool) env('DOKU_ENABLED', false),
        'client_id'      => env('DOKU_CLIENT_ID'),
        'secret_key'     => env('DOKU_SECRET_KEY'),
        // https://api-sandbox.doku.com for testing, https://api.doku.com when live
        'base_url'       => env('DOKU_BASE_URL', 'https://api-sandbox.doku.com'),
        // DOKU's examples use IDR; MYR still to be confirmed with DOKU for a Malaysian account
        'currency'       => env('DOKU_CURRENCY', 'MYR'),
        // Minutes the DOKU payment page stays open
        'due_minutes'    => (int) env('DOKU_PAYMENT_DUE_MINUTES', 60),
        // Limit the payment methods shown, comma separated DOKU codes (e.g. CREDIT_CARD); empty = all
        'methods'        => array_filter(explode(',', (string) env('DOKU_PAYMENT_METHODS', ''))),
        // Booking statuses where the customer may pay the deposit (when to pay is still the client's choice)
        'pay_statuses'   => array_filter(explode(',', (string) env('DOKU_PAY_STATUSES', 'Quote,Open'))),
    ],

];
