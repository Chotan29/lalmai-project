<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Student\RegistrationPaymentController;

\Log::info('[TEST] Starting student creation test');

// Simulate payment data
$paymentData = [
    'student_type' => 'new',
    'amount' => 1000,
    'registration_data' => [
        'first_name' => 'LocalTest',
        'last_name' => 'Student',
        'date_of_birth' => '2002-05-15',
        'email' => 'localtest' . time() . '@example.com',
        'mobile_1' => '01712345678',
        'faculty' => 32, // Science
        'semester' => 156,
        'batch' => 41,
        'gender' => 'Male',
    ],
    'payment_method' => 'ssl',
    'initiated_at' => \Carbon\Carbon::now()->toDateTimeString()
];

// Resolve controller from container
$controller = app()->make(RegistrationPaymentController::class);

// Use reflection to call private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('createStudentAndFeeRecord');
$method->setAccessible(true);

\Log::info('[TEST] Calling createStudentAndFeeRecord', ['student_type' => $paymentData['student_type']]);

$result = $method->invoke($controller, $paymentData, 'TEST-SSL-' . time(), 'SSLCommerz');

\Log::info('[TEST] Student creation result', [
    'success' => $result['success'],
    'message' => $result['message'],
    'student_id' => $result['student_id'] ?? null,
]);

echo "Student creation result:\n";
echo "  Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
echo "  Message: " . $result['message'] . "\n";
if ($result['success']) {
    echo "  Student ID: " . $result['student_id'] . "\n";
}

echo "\n[PAYMENT_TRACE] logs:\n";
system('grep -E "\[PAYMENT_TRACE\]|\[TEST\]" storage/logs/laravel.log | tail -20');
