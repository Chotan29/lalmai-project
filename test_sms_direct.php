<?php
/**
 * Test: Directly dispatch SendAttendanceNotification job and run it
 * Run: php test_sms_direct.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Jobs\AttendanceJobs\SendAttendanceNotification;
use Illuminate\Support\Facades\DB;

echo "=== Direct SMS Notification Test ===\n\n";

// Find attendance ID 1 (SAMIYA, today)
$att = Attendance::find(1);
if (!$att) { echo "Attendance ID 1 not found\n"; exit(1); }

// Make sure it has a valid status
if (!$att->attendance_status_id) {
    DB::table('attendances')->where('id', 1)->update(['attendance_status_id' => 1]);
    $att = Attendance::find(1);
}

$statusModel = AttendanceStatus::find($att->attendance_status_id);
echo "Attendance ID: {$att->id}\n";
echo "Student ID:    {$att->attendable_id}\n";
echo "Date:          {$att->date}\n";
echo "Status:        " . ($statusModel ? "{$statusModel->code} - {$statusModel->label}" : "N/A") . "\n";
echo "Current notif_status: {$att->notification_status}\n\n";

// Reset notification_status so job runs fresh
DB::table('attendances')->where('id', 1)->update([
    'notification_status' => 'idle',
    'updated_at' => now(),
]);

// Dispatch the job
$jobsBefore = DB::table('jobs')->where('queue', 'notifications')->count();
SendAttendanceNotification::dispatch(1)->onQueue('notifications');
$jobsAfter = DB::table('jobs')->where('queue', 'notifications')->count();

echo "Jobs before dispatch: {$jobsBefore}\n";
echo "Jobs after dispatch:  {$jobsAfter}\n";

if ($jobsAfter > $jobsBefore) {
    echo "\n✓ Job queued successfully!\n";
    echo "Running queue worker now...\n\n";
} else {
    echo "\n⚠ Job not queued — check queue config.\n";
    exit(1);
}
