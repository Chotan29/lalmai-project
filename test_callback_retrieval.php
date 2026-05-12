<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tempRef = $argv[1] ?? null;

if (!$tempRef) {
    echo "No reference provided\n";
    exit(1);
}

echo "Cache driver: " . config('cache.default') . "\n";
echo "Looking for key: registration_payment_data:$tempRef\n";

$cached = \Cache::get('registration_payment_data:' . $tempRef);

if ($cached) {
    echo "✓ FOUND in cache!\n";
    echo "   Data: " . json_encode($cached) . "\n";
} else {
    echo "✗ NOT found in cache!\n";
}
