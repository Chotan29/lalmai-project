<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'cybersource' => [
            'access_key' => env('CYBERSOURCE_ACCESS_KEY'),
            'profile_id' => env('CYBERSOURCE_PROFILE_ID'),
            'secret_key' => env('CYBERSOURCE_SECRET_KEY'),
            'test_mode' => env('CYBERSOURCE_TEST_MODE', true),
        ],
    'sslcommerz' => [
        'store_id'       => env('SSLCOMMERZ_STORE_ID', env('SSLCOMMERZ_STOREID', env('SSL_STORE_ID'))),
        'store_password' => env('SSLCOMMERZ_STORE_PASSWORD', env('SSLCOMMERZ_STORE_PASSWD', env('SSL_STORE_PASSWORD'))),
        'is_live'        => filter_var(env('SSLCOMMERZ_IS_LIVE', false), FILTER_VALIDATE_BOOLEAN),
        'currency'       => env('SSLCOMMERZ_CURRENCY', 'BDT'),
        'live_base_url'    => env('SSLCOMMERZ_LIVE_BASE_URL', 'https://securepay.sslcommerz.com'),
        'sandbox_base_url' => env('SSLCOMMERZ_SANDBOX_BASE_URL', 'https://sandbox.sslcommerz.com'),
    ],

];
