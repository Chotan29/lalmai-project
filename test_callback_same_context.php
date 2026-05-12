<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;

echo "=== Testing callback handler in same Laravel context ===\n\n";

// Simulate payment data storage (as would happen in pay() action)
$tempRef = 'REG-CONTEXT-' . time();
$paymentData = [
    'student_type' => 'new',
    'amount' => 500,
    'registration_data' => [
        'first_name' => 'ContextTest',
        'last_name' => 'Student',
        'email' => 'context' . time() . '@test.com',
        'mobile_1' => '01700000000',
        'faculty' => 32,
        'semester' => 156,
        'batch' => 41,
        'gender' => 'Male',
    ]
];

echo "1. Storing payment data with ref: $tempRef\n";
\Cache::put('registration_payment_data:' . $tempRef, $paymentData, now()->addHours(6));
\Log::info('[TEST] Data cached', ['ref' => $tempRef]);

// Now simulate callback
$tranId = 'SSL-CONTEXT-' . time();
$requestParams = [
    'tran_id' => $tranId,
    'value_a' => $tempRef,
    'value_b' => 'new',
    'status' => 'VALIDATED',
    'amount' => 500,
];

echo "2. Simulating callback with tranId: $tranId\n";

$request = Request::create(
    'http://localhost/registration-payment/ssl-success',
    'GET',
    $requestParams
);

// Call the controller
$controller = app()->make('App\\Http\\Controllers\\Student\\RegistrationPaymentController');
$response = $controller->sslSuccess($request);

echo "3. Controller response: " . class_basename($response) . "\n";

if ($response instanceof \Illuminate\Http\RedirectResponse) {
    echo "   Redirect to: " . $response->getTargetUrl() . "\n";
}

echo "\n4. Checking logs for success indicators:\n";
system('grep -E "\[PAYMENT_TRACE\] (Payment data|Student record|creation completed|Data recovered)" storage/logs/laravel.log | tail -5');
