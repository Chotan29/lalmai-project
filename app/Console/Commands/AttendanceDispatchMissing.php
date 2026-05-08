<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Jobs\AttendanceJobs\SendAttendanceNotification;

class AttendanceDispatchMissing extends Command
{
    protected $signature = 'attendance:dispatch-missing {--limit=1000}';
    protected $description = 'Dispatch notifications for student attendances lacking jobs.';

    public function handle()
    {
        $limit = (int)$this->option('limit');

        // Fetch candidate rows:
        // - student only
        // - has status
        // - (notification_status in idle/failed) OR meta['notify']['last_status'] != current status code
        $rows = Attendance::query()
            ->whereIn('attendable_type', [\App\Models\Student::class, 'student'])
            ->whereNotNull('attendance_status_id')
            ->where(function($q) {
                $q->whereIn('notification_status', ['idle','failed'])
                  ->orWhereNull('meta->notify->last_status')
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(meta, '$.notify.last_status')) <> (SELECT code FROM attendance_statuses WHERE attendance_statuses.id = attendances.attendance_status_id LIMIT 1)");
            })
            ->orderBy('id')
            ->limit($limit)
            ->get(['id','attendance_status_id','meta','notification_status']);

        foreach ($rows as $row) {
            // mark pending & stamp last_status
            $code = AttendanceStatus::whereKey($row->attendance_status_id)->value('code');
            $meta = is_array($row->meta) ? $row->meta : [];
            $meta['notify']['last_status'] = $code;
            $meta['notify']['queued_at']   = now()->toDateTimeString();

            Attendance::whereKey($row->id)->update([
                'notification_status' => 'pending',
                'meta' => $meta,
            ]);

            SendAttendanceNotification::dispatch($row->id)->onQueue('notifications');
        }

        $this->info('Queued: '.$rows->count());
    }
}