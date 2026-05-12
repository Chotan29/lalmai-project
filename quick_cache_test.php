<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$key = 'registration_payment_data:QUICKTEST';
$value = ['student' => 'test'];

\Cache::put($key, $value, now()->addMinutes(5));
$retrieved = \Cache::get($key);

echo "Store and retrieve test: " . ($retrieved ? "PASS" : "FAIL") . "\n";
if ($retrieved) {
    echo "Retrieved value: " . json_encode($retrieved) . "\n";
}
