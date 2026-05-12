<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PROCESS 1: PAYMENT INITIATION ===\n";
echo "Current cache driver: " . config('cache.default') . "\n\n";

// Simulate payment initiation storing cache
$tempRef = 'REG-MULTIPROC-' . time();
$paymentData = [
    'student_type' => 'new',
    'amount' => 500,
    'email' => 'test@example.com',
    'timestamp' => date('Y-m-d H:i:s')
];

echo "1. Storing in cache with key: registration_payment_data:$tempRef\n";
\Cache::put('registration_payment_data:' . $tempRef, $paymentData, now()->addHours(6));
echo "   ✓ Cached successfully\n\n";

echo "=== SIMULATING SEPARATE PROCESS (CALLBACK) ===\n";

// Now simulate a separate PHP process retrieving the same cache
// We'll do this by creating a new app instance
echo "2. Creating new app instance (simulating callback process)...\n";

// Flush the current app cache
\Cache::flush();
echo "   Old app cache flushed\n";

// Simulate the callback handler in a new request context
$callbackFile = '/c/xampp/htdocs/lalmai/test_callback_retrieval.php';
$output = shell_exec('/c/xampp/php/php "' . $callbackFile . '" "' . $tempRef . '" 2>&1');

echo "Callback process output:\n";
echo "========================\n";
echo $output;

echo "\n✅ Multi-process cache test complete\n";
