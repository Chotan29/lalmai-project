<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$testRef = 'REG-PROCTEST-' . time();
$testData = ['status' => 'stored_at_' . date('Y-m-d H:i:s')];

echo "=== PROCESS 1: STORING ===\n";
echo "Cache driver: " . config('cache.default') . "\n";
echo "Storing with key: registration_payment_data:$testRef\n";

\Cache::put('registration_payment_data:' . $testRef, $testData, now()->addHours(6));
echo "✓ Stored\n";

// Save ref
file_put_contents('/tmp/cache_test_ref.txt', 'registration_payment_data:' . $testRef);
echo "Ref written to /tmp/cache_test_ref.txt\n";
