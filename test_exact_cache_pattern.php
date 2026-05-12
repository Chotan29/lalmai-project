<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Str;
use Carbon\Carbon;

// Exact pattern from pay() method
$tempPaymentRef = 'REG-' . Str::random(10) . '-' . time();
$paymentPayload = [
    'student_type' => 'new',
    'amount' => 500,
    'registration_data' => [
        'first_name' => 'Test',
        'email' => 'test@example.com'
    ],
    'payment_method' => 'ssl',
    'initiated_at' => Carbon::now()->toDateTimeString()
];

echo "Testing exact cache pattern:\n\n";
echo "1. Temp Ref: $tempPaymentRef\n";
echo "2. Storing with key: registration_payment_data:$tempPaymentRef\n";

// Use exact pattern from controller
\Illuminate\Support\Facades\Cache::put('registration_payment_data:' . $tempPaymentRef, $paymentPayload, now()->addHours(6));
echo "   ✓ Stored\n";

// Exact pattern from sslSuccess() method
$lookupRef = $tempPaymentRef;
echo "\n3. Retrieving with key: registration_payment_data:$lookupRef\n";
$cached = \Illuminate\Support\Facades\Cache::get('registration_payment_data:' . $lookupRef);

if ($cached) {
    echo "   ✓ Found!\n";
    echo "   Data: " . json_encode($cached) . "\n";
} else {
    echo "   ✗ NOT Found\n";
}

// Try with db cache driver instead
echo "\n4. Switching to database cache...\n";
config(['cache.default' => 'database']);
$tempRef2 = 'REG-' . Str::random(10) . '-' . time();
\Illuminate\Support\Facades\Cache::put('registration_payment_data:' . $tempRef2, $paymentPayload, now()->addHours(6));
$found = \Illuminate\Support\Facades\Cache::get('registration_payment_data:' . $tempRef2);
echo "   Database cache: " . ($found ? "WORKS" : "DOESN'T WORK") . "\n";
