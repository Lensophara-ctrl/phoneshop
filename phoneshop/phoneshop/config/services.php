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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
    ],

    'bakong' => [
        'api_url' => env('BAKONG_API_URL', 'https://api-bakong.nbc.gov.kh'),
        'token' => env('BAKONG_TOKEN'),
        'merchant' => [
            'bakong_id' => env('MERCHANT_BAKONG_ID'),
            'name' => env('MERCHANT_NAME', 'PhoneShop'),
            'city' => env('MERCHANT_CITY', 'Phnom Penh'),
            'acquiring_bank' => env('ACQUIRING_BANK', 'ABA'),
        ],
        // Bakong Checkout (Dynamic Payment)
        'checkout' => [
            'enabled' => env('BAKONG_CHECKOUT_ENABLED', false),
            'api_url' => env('BAKONG_CHECKOUT_URL', 'https://checkout-bakong.nbc.gov.kh'),
            'merchant_id' => env('BAKONG_CHECKOUT_MERCHANT_ID'),
            'callback_url' => env('BAKONG_CHECKOUT_CALLBACK_URL'),
            'return_url' => env('BAKONG_CHECKOUT_RETURN_URL'),
        ],
    ],

];
