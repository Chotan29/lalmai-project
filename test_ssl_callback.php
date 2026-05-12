<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "[TEST] Complete SSL callback flow test\n";
echo "=====================================\n\n";

// Simulate payment data
$tempPaymentRef = 'REG-TEST' . time();
$paymentData = [
    'student_type' => 'new',
    'amount' => 500,
    'registration_data' => [
        'first_name' => 'CallbackTest',
        'last_name' => 'Student',
        'date_of_birth' => '2003-08-20',
        'email' => 'callbacktest' . time() . '@example.com',
        'mobile_1' => '01798765432',
        'faculty' => 32, 
        'semester' => 156,
        'batch' => 41,
        'gender' => 'Female',
    ],
    'payment_method' => 'ssl',
    'initiated_at' => \Carbon\Carbon::now()->toDateTimeString()
];

// Store in cache
\Cache::put('registration_payment_data:' . $tempPaymentRef, $paymentData, now()->addHours(6));

echo "1. Cached payment data with ref: $tempPaymentRef\n";

// Create mock SSL callback data
$tranId = 'SSL-CB-' . time();
$callbackData = [
    'tran_id' => $tranId,
    'value_a' => $tempPaymentRef,
    'value_b' => 'new',
    'status' => 'VALIDATED',
    'amount' => 500,
    'currency' => 'BDT',
];

echo "2. Simulating SSL callback with tranId: $tranId\n";
echo "   - value_a (ref): $tempPaymentRef\n";
echo "   - value_b (type): new\n";
echo "   - status: VALIDATED\n\n";

// Build callback URL
$queryString = http_build_query($callbackData);
$callbackUrl = "http://127.0.0.1:8000/registration-payment/ssl-success?" . $queryString;

echo "3. Making HTTP request to callback endpoint...\n";
echo "   URL: $callbackUrl\n\n";

// Use curl to test the callback
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $callbackUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_HEADER => true,
    CURLOPT_TIMEOUT => 10,
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "4. Response HTTP Code: $httpCode\n";

// Check for redirect
if ($httpCode >= 300 && $httpCode < 400) {
    preg_match('/Location: (.*)/', $response, $matches);
    if ($matches) {
        echo "   ✓ Redirect to: " . trim($matches[1]) . "\n";
    }
}

echo "\n5. [PAYMENT_TRACE] entries captured:\n";
echo "====================================\n";
system('grep -E "\[PAYMENT_TRACE\]" storage/logs/laravel.log | tail -15');

