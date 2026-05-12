<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test logging
\Illuminate\Support\Facades\Log::info('[PAYMENT_TRACE] Testing trace logging', ['test_field' => 'value']);
echo "✓ Logging test sent\n";

// Check if log was written
$logs = file_get_contents('storage/logs/laravel.log');
if (strpos($logs, '[PAYMENT_TRACE]') !== false) {
    echo "✓ [PAYMENT_TRACE] found in logs\n";
} else {
    echo "✗ [PAYMENT_TRACE] NOT found\n";
}

// Show last few lines
echo "\nLast log lines:\n";
system('tail -5 storage/logs/laravel.log');
