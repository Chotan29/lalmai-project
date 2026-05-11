<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        // Your existing excluded routes (if any)
        'account/fees/pay-with-stripe',
        'account/fees/pay-with-khalti',
        'account/fees/payumoney-form',
        'account/fees/pay-with-payumoney/success',
        'account/fees/pay-with-payumoney/failure',
        'account/fees/pesapal-form',
        'account/fees/pay-with-pesapal',

        // SSLCommerz exemptions (based on your provided SslCommerzPaymentController)
        // These should match the actual routes defined in your web.php for SSLCommerz callbacks
        'account/fees/sslcommerz-success',
        'account/fees/sslcommerz-fail',
        'account/fees/sslcommerz-cancel',
        'account/fees/sslcommerz-ipn',
        'account/fees/sslcommerz*', // General wildcard for SSLCommerz if needed

        // UNITED COMMERCIAL BANK LIMITED (UCBL) exemptions
        // These are based on the 'ucbl-pay' prefix defined in your routes/web.php
        // 'account/fees/ucbl-pay/success',
        // 'account/fees/ucbl-pay/fail',
        // 'account/fees/ucbl-pay/cancel',
        // 'account/fees/ucbl-pay/ipn',
        // A more general wildcard if all routes under 'ucbl-pay' should be excluded
        //'account/fees/ucbl-pay/*',
        // CyberSource exemptions
       // 'account/fees/cybersource*',
        'ucb/payment-confirmation',

        // Public online registration payment callbacks
        'registration-payment/ssl-success',
        'registration-payment/ssl-fail',
        'registration-payment/ssl-cancel',
        'registration-payment/ssl-ipn',
        'registration-payment/ucb-success',
        'registration-payment/ucb-cancel',


        //Attendance TIPSOI 
        'attendance/tipsoi-sdk/heartbeat',
        'attendance/tipsoi-sdk/fingerprint-callback',

    ];
}
