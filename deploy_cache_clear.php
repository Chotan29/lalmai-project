<?php
// Connect to server and run cache clear
chdir('/home/lalma87b/ims.lalmaigc.edu.bd');
$output = [];
$output[] = "Current directory: " . getcwd();

// Run php artisan optimize:clear
exec('php artisan optimize:clear 2>&1', $out1);
$output = array_merge($output, $out1);
$output[] = "---";

// Run php artisan config:cache
exec('php artisan config:cache 2>&1', $out2);
$output = array_merge($output, $out2);
$output[] = "---";

// Verify with a test
$output[] = "Checking loaded files count...";
$files = glob('storage/framework/cache/*');
$output[] = "Cache files: " . count($files);

echo implode("\n", $output);
?>
