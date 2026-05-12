<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

echo "=== Testing ngrok URL Configuration ===\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "Registration Payment Success URL: " . route('registration-payment.ssl-success') . "\n";
echo "Registration Payment Fail URL: " . route('registration-payment.ssl-fail') . "\n\n";

// Test that URL is accessible from external (this would be called by SSL Commerz)
$testUrl = route('registration-payment.ssl-success');
echo "Testing if callback URL is accessible:\n";
echo "URL: $testUrl\n";

// Simulate an SSL Commerz callback
echo "\n=== Simulating SSL Commerz Callback ===\n";

$testPaymentData = [
    'amount' => '100',
    'card_holder' => 'Test User',
    'status' => 'VALID',
    'tran_date' => date('Y-m-d H:i:s'),
    'tran_id' => 'TEST-' . time(),
    'currency' => 'BDT',
    'card_type' => 'Visa',
    'base_fair' => '100',
    'value_a' => '',
    'value_b' => '',
    'value_c' => '',
    'value_d' => '',
];

Log::info('Test callback data:', $testPaymentData);
echo "Test payment data logged to: storage/logs/laravel.log\n";
echo "Check the logs to verify configuration is correct.\n";
