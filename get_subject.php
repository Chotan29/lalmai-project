<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$subject = DB::table('subjects')->where('id', 1227)->first();
if ($subject) {
    echo json_encode($subject);
} else {
    echo "Subject not found";
}
