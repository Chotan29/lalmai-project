<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Cache test with FILE driver:\n";
echo "============================\n\n";

// Verify cache driver
echo "1. Current cache driver: " . config('cache.default') . "\n";

$testRef = 'REG-FILETEST-' . time();
$testData = ['student' => 'Test', 'amount' => 500, 'time' => date('Y-m-d H:i:s')];

echo "2. Storing with key: registration_payment_data:$testRef\n";
$stored = \Cache::put('registration_payment_data:' . $testRef, $testData, now()->addHours(6));
echo "   Store result: " . ($stored ? 'SUCCESS' : 'FAILED') . "\n";

echo "\n3. Cache directory contents:\n";
$cacheDir = storage_path('framework/cache');
system('ls -la "' . $cacheDir . '" 2>/dev/null | tail -5');

echo "\n4. Retrieving from same process:\n";
$retrieved = \Cache::get('registration_payment_data:' . $testRef);
echo "   Result: " . ($retrieved ? 'FOUND' : 'NOT FOUND') . "\n";
if ($retrieved) {
    echo "   Data: " . json_encode($retrieved) . "\n";
}

// Now call the controller in the same process to see if it can retrieve
echo "\n5. Testing controller cache lookup in same process:\n";

$reflection = new ReflectionClass('App\\Http\\Controllers\\Student\\RegistrationPaymentController');
$cacheGetter = $reflection->getMethod('getCachedPaymentData');
$cacheGetter->setAccessible(true);

$controller = app()->make('App\\Http\\Controllers\\Student\\RegistrationPaymentController');
$foundData = $cacheGetter->invoke($controller, 'registration_payment_data:' . $testRef);
echo "   Controller found: " . ($foundData ? 'YES' : 'NO') . "\n";

