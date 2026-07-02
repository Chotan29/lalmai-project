<?php

// return [
//     'payment_url' => env('CYBERSOURCE_PAYMENT_URL','https://testsecureacceptance.cybersource.com/pay'),
//     'access_key' => env('CYBERSOURCE_ACCESS_KEY'),
//     'profile_id' => env('CYBERSOURCE_PROFILE_ID'),
//     'secret_key' => env('CYBERSOURCE_SECRET_KEY'),
// ];


return [
    'access_key'    => env('CYBERSOURCE_ACCESS_KEY'),
    'profile_id'    => env('CYBERSOURCE_PROFILE_ID'),
    'secret_key'    => env('CYBERSOURCE_SECRET_KEY'),
    'payment_url'   => env('CYBERSOURCE_PAYMENT_URL', 'https://testsecureacceptance.cybersource.com/pay'),
    'callback_url'  => env('CYBERSOURCE_CALLBACK_URL'),
    // ... any other config settings
];