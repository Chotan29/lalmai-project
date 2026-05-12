<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$testRef = 'REG-CACHE-TEST' . time();
$testData = ['name' => 'Test', 'amount' => 500];

echo "Testing cache operations:\n";
echo "=========================\n\n";

// Store
echo "1. Storing cache with key: registration_payment_data:$testRef\n";
\Cache::put('registration_payment_data:' . $testRef, $testData, now()->addHours(6));

// Retrieve immediately
echo "2. Retrieving immediately...\n";
$retrieved1 = \Cache::get('registration_payment_data:' . $testRef);
if ($retrieved1) {
    echo "   ✓ Found: " . json_encode($retrieved1) . "\n";
} else {
    echo "   ✗ Not found!\n";
}

// Wait 1 second and retrieve again
sleep(1);
echo "3. Retrieving again after 1 second...\n";
$retrieved2 = \Cache::get('registration_payment_data:' . $testRef);
if ($retrieved2) {
    echo "   ✓ Found: " . json_encode($retrieved2) . "\n";
} else {
    echo "   ✗ Not found!\n";
}

// Check cache driver
echo "\n4. Cache driver: " . config('cache.default') . "\n";

// Check cache files
$cacheDriver = config('cache.default');
if ($cacheDriver === 'file') {
    $cacheDir = storage_path('framework/cache');
    echo "   Cache directory: $cacheDir\n";
    echo "   Files in cache: \n";
    system('ls -la ' . $cacheDir . ' 2>/dev/null | grep -i registration | head -5');
}

