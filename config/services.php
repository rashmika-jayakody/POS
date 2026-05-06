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

    /*
    |--------------------------------------------------------------------------
    | PayHere — keep checkout and Merchant API credentials separate
    |--------------------------------------------------------------------------
    |
    | Checkout (hosted form + md5 hash): merchant_id + merchant_secret only.
    | Merchant API (OAuth + Subscription Manager): app_id + app_secret and/or
    | pre-encoded Basic auth. Do not use API fields when building checkout.
    |
    */

    'payhere_checkout' => [
        'merchant_id' => env('PAYHERE_MERCHANT_ID'),
        'merchant_secret' => env('PAYHERE_MERCHANT_SECRET'),
        'sandbox' => env('PAYHERE_SANDBOX', true),
        'currency' => env('PAYHERE_CURRENCY', 'LKR'),
        'debug' => env('PAYHERE_CHECKOUT_DEBUG', false),
        'return_url' => env('PAYHERE_RETURN_URL'),
        'cancel_url' => env('PAYHERE_CANCEL_URL'),
        'notify_url' => env('PAYHERE_NOTIFY_URL'),
        'grace_days' => env('PAYHERE_GRACE_DAYS', 7),
    ],

    'payhere_api' => [
        'app_id' => env('PAYHERE_APP_ID'),
        'app_secret' => env('PAYHERE_APP_SECRET'),
        /** Base64(App ID:App Secret); optional if app_id + app_secret are set */
        'basic_auth' => env('PAYHERE_MERCHANT_BASIC_AUTH'),
        'sandbox' => env('PAYHERE_SANDBOX', true),
    ],

];
