<?php
/**
 * Test: Attendance save -> auto notification job queued -> queue worker -> SMS
 * Run: php test_attendance_sms.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

echo "=== Attendance SMS Notification Test ===\n\n";

// 1. Find SAMIYA's student record
$student = Student::find(4423);
if (!$student) { echo "ERROR: Student 4423 not found\n"; exit(1); }
echo "Student: {$student->first_name} (reg: {$student->reg_no})\n";

// 2. Guardian phone
$guardian = DB::table('student_guardians as sg')
    ->join('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
    ->where('sg.students_id', 4423)
    ->whereNotNull('gd.guardian_mobile_1')
    ->where('gd.guardian_mobile_1', '!=', '')
    ->select('gd.guardian_first_name', 'gd.guardian_mobile_1')
    ->first();
if ($guardian) {
    echo "Guardian: {$guardian->guardian_first_name} -> {$guardian->guardian_mobile_1}\n";
} else {
    echo "WARNING: No guardian phone found\n";
}

// 3. Today's attendance
$today = now()->toDateString();
$att = Attendance::where('attendable_type', Student::class)
    ->where('attendable_id', 4423)
    ->whereDate('date', $today)
    ->first();

$beforeJobs = DB::table('jobs')->where('queue', 'notifications')->count();
echo "\nJobs in 'notifications' queue BEFORE: {$beforeJobs}\n";

if ($att) {
    echo "Existing attendance ID: {$att->id}, status_id: {$att->attendance_status_id}, notif: {$att->notification_status}\n";
    // Toggle status to force isDirty('attendance_status_id') = true
    $newStatusId = ($att->attendance_status_id == 1) ? 2 : 1;
    $att->notification_status = 'idle';
    $att->attendance_status_id = $newStatusId;
    $att->save();
    echo "Updated status_id to: {$newStatusId}\n";
} else {
    echo "No attendance today — creating new row\n";
    $att = new Attendance();
    $att->attendable_type      = Student::class;
    $att->attendable_id        = 4423;
    $att->reg_no               = $student->reg_no;
    $att->date                 = $today;
    $att->attendance_status_id = 1; // Present
    $att->source               = 'device';
    $att->check_in_at          = now()->toDateTimeString();
    $att->created_by           = 1;
    $att->updated_by           = 1;
    $att->save();
    echo "Created attendance ID: {$att->id}\n";
}

$afterJobs = DB::table('jobs')->where('queue', 'notifications')->count();
echo "Jobs in 'notifications' queue AFTER:  {$afterJobs}\n";

$fresh = Attendance::find($att->id);
echo "notification_status on DB: {$fresh->notification_status}\n";

if ($afterJobs > $beforeJobs) {
    echo "\n✓ Job queued! Now run:\n";
    echo "  php artisan queue:work --queue=notifications --once --timeout=30\n\n";
    echo "Then check storage/logs/laravel.log for SMS result.\n";
} else {
    echo "\n⚠ No new job added. Check if notification_status was already 'pending'.\n";
    // Show job details
    $jobs = DB::table('jobs')->where('queue','notifications')->get();
    foreach ($jobs as $j) {
        $payload = json_decode($j->payload, true);
        echo "  Existing job: " . ($payload['displayName'] ?? 'unknown') . " id={$j->id}\n";
    }
}

echo "\n=== Done ===\n";
