<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ref = trim(file_get_contents('/tmp/cache_test_ref.txt'));

echo "=== PROCESS 2: RETRIEVING ===\n";
echo "Cache driver: " . config('cache.default') . "\n";
echo "Looking for: $ref\n";

$found = \Cache::get($ref);

if ($found) {
    echo "✓ FOUND!\n";
    echo "Data: " . json_encode($found) . "\n";
} else {
    echo "✗ NOT FOUND\n";
}
