<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Clearing application caches...\n";
\Illuminate\Support\Facades\Artisan::call('cache:clear');
echo "✓ Cache cleared\n";

\Illuminate\Support\Facades\Artisan::call('view:clear');
echo "✓ Views cleared\n";

echo "Done. You can now access the live payment system.\n";
