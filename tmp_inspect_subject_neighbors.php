<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = Illuminate\Support\Facades\DB::table('subjects')
    ->whereBetween('id', [1038, 1052])
    ->orderBy('id')
    ->get();

foreach ($rows as $row) {
    echo implode('|', [
        $row->id,
        $row->title ?? '',
        $row->status ?? '',
        $row->code ?? '',
        $row->created_by ?? '',
        $row->last_updated_by ?? '',
    ]) . PHP_EOL;
}