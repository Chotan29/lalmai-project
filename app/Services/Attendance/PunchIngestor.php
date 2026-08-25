<?php

namespace App\Services\Attendance;

use App\Models\AttendanceStatus;
use App\Models\Student;
use App\Models\TipsoiAttendanceLog;
use App\Services\Devices\PunchData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * A recognised face becomes an attendance row, and a guardian gets told.
 *
 * Shared by every device brand. Nothing here knows what made the punch - it is handed a
 * PunchData and that is all it ever sees, which is what keeps a second manufacturer from
 * reaching into the attendance rules.
 *
 * Written to be cheap. This account is allowed twenty-five database connections and cannot have
 * more, and on a school morning a thousand students walk past the camera inside half an hour.
 * Every query here is counted: find the student, find today's row, write it, queue the message.
 * Four, and none of them scan a table.
 */
class PunchIngestor
{
    /** Status ids are looked up once per request, not once per punch. */
    protected static $statusCache = [];

    /**
     * @return array [bool stored, string reason]
     */
    public function ingest(PunchData $punch)
    {
        /* Keep the raw punch whatever happens next. A stranger at the door, a person we cannot
           match, a duplicate - all worth having when somebody asks why a card did not register.
           This is also the only copy if the mapping below turns out to be wrong. */
        $this->recordRaw($punch);

        if (!$punch->isIdentified()) {
            return [false, 'not an identified person'];
        }

        $student = $this->findStudent($punch->personId);
        if (!$student) {
            /* The device knows an id we do not. Says the enrolment and the student list have
               drifted apart - worth a log line, never worth inventing a student. */
            Log::warning('Attendance punch for an unknown person', [
                'personId'  => $punch->personId,
                'deviceKey' => $punch->deviceKey,
            ]);
            return [false, 'no student with that id'];
        }

        $date = $punch->at->toDateString();

        $existing = DB::table('attendances')
            ->where('attendable_type', Student::class)
            ->where('attendable_id', $student->id)
            ->whereDate('date', $date)
            ->first(['id', 'check_in_at', 'check_out_at']);

        if (!$existing) {
            return $this->firstPunchOfDay($student, $punch, $date);
        }

        return $this->laterPunchOfDay($existing, $punch);
    }

    /* ---------------- first sighting today ---------------- */

    protected function firstPunchOfDay($student, PunchData $punch, $date)
    {
        $statusId = $this->statusIdFor($punch);

        /* Written with the query builder rather than the model on purpose.
           Attendance::save() sends the guardian message inline when it is not running in the
           console - and this runs inside the device's own http request. The device would sit
           waiting on an sms gateway, holding a database connection, once per student. So the row
           goes in directly and the message is queued below, which is what the scheduler's
           queue:work is there for. */
        $id = DB::table('attendances')->insertGetId([
            'date'                 => $date,
            'attendable_type'      => Student::class,
            'attendable_id'        => $student->id,
            'reg_no'               => $student->reg_no,
            'source'               => $this->sourceFor($punch),
            'attendance_status_id' => $statusId,
            'check_in_at'          => $punch->at->toDateTimeString(),
            'notification_status'  => 'idle',
            'meta'                 => json_encode([
                'device' => [
                    'key'      => $punch->deviceKey,
                    'personId' => $punch->personId,
                    'at'       => $punch->at->toDateTimeString(),
                ],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->queueGuardianMessage($id);

        return [true, 'checked in'];
    }

    /* ---------------- they have already been seen today ---------------- */

    protected function laterPunchOfDay($existing, PunchData $punch)
    {
        $debounce = (int) config('devices.punch_debounce_seconds', 120);
        $lastSeen = $existing->check_out_at ?: $existing->check_in_at;

        if ($lastSeen && $punch->at->diffInSeconds(\Carbon\Carbon::parse($lastSeen)) < $debounce) {
            /* Somebody standing in front of the camera for a few seconds is one arrival, not
               five. Without this a single student produces a row of messages to one guardian. */
            return [false, 'ignored, within the debounce window'];
        }

        $checkoutAfter = (int) config('devices.checkout_after_minutes', 180);
        $checkIn = $existing->check_in_at ? \Carbon\Carbon::parse($existing->check_in_at) : null;

        if ($checkIn && $checkIn->diffInMinutes($punch->at) < $checkoutAfter) {
            /* Too soon after arriving to be a departure - a second pass through the gate. */
            return [false, 'ignored, too soon to be a departure'];
        }

        DB::table('attendances')->where('id', $existing->id)->update([
            'check_out_at' => $punch->at->toDateTimeString(),
            'updated_at'   => now(),
        ]);

        return [true, 'checked out'];
    }

    /* ---------------- pieces ---------------- */

    protected function recordRaw(PunchData $punch)
    {
        try {
            /* Column names are the ones already on tipsoi_attendance_logs - the table was built
               for the vendor's cloud sync and is reused here rather than adding a second one. */
            TipsoiAttendanceLog::create([
                'device_identifier'   => $punch->deviceKey,
                'person_identifier'   => $punch->personId,
                'person_id_in_device' => $punch->personId,
                'person_name'         => $punch->name,
                'logged_time'         => $punch->at->toDateTimeString(),
                'sync_time'           => now(),
                'type'                => 'device_push',
                'raw_data'            => $punch->raw,
            ]);
        } catch (\Throwable $e) {
            /* The raw log is a convenience. Losing it must never lose the attendance. */
            Log::warning('Could not write raw punch', ['error' => $e->getMessage()]);
        }
    }

    /**
     * attendances.source is an enum, and MySQL stores an empty string for a value not on the
     * list instead of refusing it - so a wrong value here is invisible until somebody filters a
     * report by capture method and finds a gap. The permitted value per brand is in config.
     */
    protected function sourceFor(PunchData $punch)
    {
        $vendor = $punch->vendor ?: config('devices.default');

        return config("devices.vendors.{$vendor}.attendance_source", 'manual');
    }

    protected function findStudent($personId)
    {
        /* Enrolment sends the student's own id to the device, so this is a primary key lookup.
           Falling back to reg_no covers anyone enrolled by hand from the device screen. */
        if (ctype_digit((string) $personId)) {
            $student = Student::select('id', 'reg_no')->find((int) $personId);
            if ($student) { return $student; }
        }

        return Student::select('id', 'reg_no')->where('reg_no', $personId)->first();
    }

    /**
     * Present, or late if the college has a cut-off and this is after it.
     */
    protected function statusIdFor(PunchData $punch)
    {
        $lateAfter = trim((string) config('devices.late_after', ''));
        $wanted    = 'Present';

        if ($lateAfter !== '') {
            $cut = $punch->at->copy()->setTimeFromTimeString($lateAfter);
            if ($punch->at->greaterThan($cut)) { $wanted = 'Late'; }
        }

        return $this->statusId($wanted);
    }

    protected function statusId($label)
    {
        if (array_key_exists($label, self::$statusCache)) {
            return self::$statusCache[$label];
        }

        $row = AttendanceStatus::select('id')
            ->where('label', $label)->orWhere('code', $label)
            ->first();

        return self::$statusCache[$label] = $row ? $row->id : null;
    }

    /**
     * Queue the guardian message. Queued, never sent here.
     */
    protected function queueGuardianMessage($attendanceId)
    {
        try {
            DB::table('attendances')->where('id', $attendanceId)
                ->update(['notification_status' => 'pending', 'updated_at' => now()]);

            dispatch(
                (new \App\Jobs\AttendanceJobs\SendAttendanceNotification($attendanceId))
                    ->onQueue('notifications')
            );
        } catch (\Throwable $e) {
            Log::error('Could not queue attendance notification', [
                'attendance' => $attendanceId, 'error' => $e->getMessage(),
            ]);
        }
    }
}
