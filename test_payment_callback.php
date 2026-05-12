<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Student\RegistrationPaymentController;

// Create mock payment data
$tempPaymentRef = 'REG-TEST' . time();
$paymentData = [
    'student_type' => 'new',
    'amount' => 1000,
    'registration_data' => [
        'first_name' => 'Test',
        'last_name' => 'Student',
        'date_of_birth' => '2000-01-01',
        'email' => 'test' . time() . '@example.com',
        'mobile_1' => '01700000000',
        'faculty' => 32,
        'semester' => 156,
        'batch' => 41,
        'gender' => 'Male',
    ],
    'payment_method' => 'ssl',
    'initiated_at' => \Carbon\Carbon::now()->toDateTimeString()
];

// Store payment data in cache
\Illuminate\Support\Facades\Cache::put('registration_payment_data:' . $tempPaymentRef, $paymentData, now()->addHours(6));

\Log::info('[TEST] Starting payment callback simulation', [
    'temp_ref' => $tempPaymentRef,
    'cached' => true,
]);

// Create mock request with callback data
$queryParams = [
    'tran_id' => 'SSL-TEST-' . time(),
    'value_a' => $tempPaymentRef,
    'value_b' => 'new',
    'status' => 'VALIDATED',
    'amount' => 1000,
];

$request = Request::create('http://127.0.0.1:8000/registration-payment/ssl-success', 'GET', $queryParams);

// Create a session for the request
$session = new \Illuminate\Session\Store(
    'test_session',
    new \Illuminate\Session\Middleware\StartSession()
);
$request->setSession(app('session.store'));
$request->session()->put('registration_payment_ref', $tempPaymentRef);

// Call controller
$controller = new RegistrationPaymentController();
$response = $controller->sslSuccess($request);

echo "✓ Callback test completed\n";
echo "Response type: " . get_class($response) . "\n";

if ($response instanceof \Illuminate\Http\RedirectResponse) {
    echo "Redirecting to: " . $response->getTargetUrl() . "\n";
}

echo "\nCheck logs for [PAYMENT_TRACE] entries:\n";
system('tail -20 storage/logs/laravel.log | grep -E "\[PAYMENT_TRACE\]|Student registered|created successfully"');
