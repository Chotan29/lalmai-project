<?php
/**
 * Remote cache/config clearing script
 * Run once then delete
 */
if (!defined('ARTISAN_CLEAR_TOKEN')) {
    $token = isset($_GET['token']) ? $_GET['token'] : '';
    if ($token !== 'lalmai_clear_2026') {
        http_response_code(403);
        die('Forbidden');
    }
}

chdir(dirname(__DIR__));
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

ob_start();

echo "=== Clearing Caches on Live Server ===\n\n";

// Clear config cache
try {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "✓ Config cache cleared\n";
} catch (\Exception $e) {
    echo "✗ Config clear failed: " . $e->getMessage() . "\n";
}

// Clear application cache
try {
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "✓ Application cache cleared\n";
} catch (\Exception $e) {
    echo "✗ Cache clear failed: " . $e->getMessage() . "\n";
}

// Clear view cache
try {
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "✓ View cache cleared\n";
} catch (\Exception $e) {
    echo "✗ View clear failed: " . $e->getMessage() . "\n";
}

// Verify settings
echo "\n=== Current Settings ===\n";
echo "APP_ENV: " . config('app.env') . "\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "CACHE_DRIVER: " . config('cache.default') . "\n";
echo "LOG_CHANNEL: " . config('logging.default') . "\n";

// Test cache works
$testKey = 'cache_test_' . time();
\Illuminate\Support\Facades\Cache::put($testKey, 'working', 60);
$retrieved = \Illuminate\Support\Facades\Cache::get($testKey);
echo "\nCache write/read test: " . ($retrieved === 'working' ? "✓ PASS" : "✗ FAIL") . "\n";

echo "\n=== DONE. Delete this file after use. ===\n";
