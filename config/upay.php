<?php

return [
    'base_url' => env('UPAY_BASE_URL', 'https://uat-pg.upay.systems/'),
    'merchant_id' => env('UPAY_MERCHANT_ID'),
    'merchant_key' => env('UPAY_MERCHANT_KEY'),
    'merchant_code' => env('UPAY_MERCHANT_CODE'),
    'merchant_country_code' => env('UPAY_MERCHANT_COUNTRY_CODE', 'BD'),
    'merchant_city' => env('UPAY_MERCHANT_CITY', 'Dhaka'),
    'merchant_category_code' => env('UPAY_MERCHANT_CATEGORY_CODE'),
    'merchant_mobile' => env('UPAY_MERCHANT_MOBILE'),
    'transaction_currency_code' => env('UPAY_TRANSACTION_CURRENCY_CODE', 'BDT'),
];