<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Jobs\AttendanceJobs\SendAttendanceNotification;

/**
 * Catch attendances whose guardian was never told, and tell them.
 *
 * Runs every five minutes, so it is the safety net for a notification that was missed - a queue
 * worker that died mid job, a row written by an import, a punch that arrived while the gateway
 * was down.
 *
 * It used to be a trap. "failed" counted as "not yet sent", so a message that could not be
 * delivered was queued again on the next sweep, and the one after, for ever. On 19 August the log
 * showed the same eight rows going to the gateway every fifteen minutes all day - harmless only
 * because the account had no credit. Adding credit would have released the lot at once and then
 * kept going.
 *
 * So now a failure is counted, and after a few tries the row is left alone. A message nobody
 * could deliver three times running needs somebody to look at the number, not a fourth attempt.
 */
class AttendanceDispatchMissing extends Command
{
    protected $signature = 'attendance:dispatch-missing {--limit=1000}';
    protected $description = 'Dispatch notifications for student attendances lacking jobs.';

    public function handle()
    {
        $limit       = (int) $this->option('limit');
        $maxAttempts = (int) config('attendance.notify_max_attempts', 3);
        $maxAgeDays  = (int) config('attendance.notify_max_age_days', 2);

        $rows = Attendance::query()
            ->whereIn('attendable_type', [\App\Models\Student::class, 'student'])
            ->whereNotNull('attendance_status_id')
            /* Old attendance is not news. Telling a parent about last week helps nobody, and
               without this the sweep keeps dragging the whole history along behind it. */
            ->whereDate('date', '>=', now()->subDays($maxAgeDays)->toDateString())
            ->where(function ($q) {
                $q->whereIn('notification_status', ['idle', 'failed'])
                  ->orWhereNull('meta->notify->last_status')
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(meta, '$.notify.last_status')) <> (SELECT code FROM attendance_statuses WHERE attendance_statuses.id = attendances.attendance_status_id LIMIT 1)");
            })
            ->orderBy('id')
            ->limit($limit)
            ->get(['id', 'attendance_status_id', 'meta', 'notification_status']);

        $queued = 0;
        $abandoned = 0;

        foreach ($rows as $row) {
            $meta = is_array($row->meta) ? $row->meta : [];
            $attempts = (int) ($meta['notify']['attempts'] ?? 0);

            if ($attempts >= $maxAttempts) {
                /* Say so on the row itself. Left as "failed" it would be picked up again on the
                   next sweep, and nobody reading the record would know it had been given up on. */
                $meta['notify']['abandoned_at'] = now()->toDateTimeString();

                Attendance::whereKey($row->id)->update([
                    'notification_status' => 'abandoned',
                    'meta' => json_encode($meta),
                ]);

                Log::info('Attendance notification abandoned after repeated failures', [
                    'attendance_id' => $row->id,
                    'attempts'      => $attempts,
                    'last_error'    => $meta['notify']['error'] ?? null,
                ]);

                $abandoned++;
                continue;
            }

            $code = AttendanceStatus::whereKey($row->attendance_status_id)->value('code');

            $meta['notify']['last_status'] = $code;
            $meta['notify']['queued_at']   = now()->toDateTimeString();
            $meta['notify']['attempts']    = $attempts + 1;

            /* mass update bypasses model casts - meta must be JSON-encoded manually */
            Attendance::whereKey($row->id)->update([
                'notification_status' => 'pending',
                'meta' => json_encode($meta),
            ]);

            SendAttendanceNotification::dispatch($row->id)->onQueue('notifications');
            $queued++;
        }

        $this->info('Queued: ' . $queued . ', abandoned: ' . $abandoned);
    }
}
