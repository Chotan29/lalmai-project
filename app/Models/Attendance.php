<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected static $legacyYearIds = [];

    protected $fillable = [
        'date','attendable_id','attendable_type','reg_no','source',
        'attendance_status_id','check_in_at','check_out_at','meta',
        'created_by','updated_by',
        'notification_status', // 'idle' | 'pending' | 'sent' | 'failed'
    ];

    protected $casts = [
        'date'         => 'date',
        'check_in_at'  => 'datetime',
        'check_out_at' => 'datetime',
        'meta'         => 'array',
    ];

    /**
     * SELF-DISPATCHING SAVE:
     * - Triggers regardless of Eloquent events (works inside Model::withoutEvents).
     * - After a successful save, if this is a student row with a status and
     *   (new row OR status changed), we enqueue a notification job.
     */
    public function save(array $options = [])
    {
        // Decide BEFORE saving if we should queue afterwards.
        $isStudent    = in_array($this->attendable_type, [Student::class, 'student'], true);
        $hasStatus    = !empty($this->attendance_status_id);
        $isNew        = !$this->exists;
        $statusChanged= $this->exists ? $this->isDirty('attendance_status_id') : true;

        $shouldQueue  = $isStudent && $hasStatus && ($isNew || $statusChanged);

        // Do the actual save
        $result = parent::save($options);

        // If saved OK and we should queue, do it in a version-safe way
        if ($result && $shouldQueue) {
            // ID-based helper avoids events and saveQuietly
            self::queueGuardianNotificationIfNeededById((int)$this->id);
        }

        return $result;
    }

    /**
     * BACKWARD-COMPAT: existing call sites can still use this.
     * Delegates to the ID-based version.
     */
    public static function queueGuardianNotificationIfNeeded(Attendance $att): void
    {
        if ($att && $att->id) {
            self::queueGuardianNotificationIfNeededById((int)$att->id);
        }
    }

    /**
     * Preferred helper (works even if the row was saved without events).
     * - Ensures student + has status
     * - Stamps meta.notify.last_status and sets notification_status='pending'
     * - Dispatches job to "notifications" queue
     * - Idempotent for the same status
     */
    public static function queueGuardianNotificationIfNeededById(int $attendanceId): void
    {
        /** @var self|null $att */
        $att = self::with('status')->find($attendanceId);
        if (!$att) return;

        if (!in_array($att->attendable_type, [Student::class, 'student'], true)) return;
        if (!$att->attendance_status_id) return;

        $code = self::statusCodeById($att->attendance_status_id);
        if (!$code) return;

        $meta = is_array($att->meta) ? $att->meta : [];
        $last = isset($meta['notify']['last_status']) ? $meta['notify']['last_status'] : null;

        // already pending/sent for same status? skip
        if (in_array((string)$att->notification_status, ['pending','sent'], true) && $last === $code) {
            return;
        }

        // Update WITHOUT firing events (no saveQuietly); safe on older Laravel
        $meta['notify']['last_status'] = $code;
        $meta['notify']['queued_at']   = now()->toDateTimeString();

        DB::table('attendances')->where('id', $att->id)->update([
            'notification_status' => 'pending',
            'meta'                => json_encode($meta),
            'updated_at'          => now(),
        ]);

        \App\Jobs\AttendanceJobs\SendAttendanceNotification::dispatch($att->id)
            ->onQueue('notifications');
    }

    /*** Relations ***/
    public function attendable(): MorphTo { return $this->morphTo(); }

    public function status(): BelongsTo
    {
        return $this->belongsTo(AttendanceStatus::class, 'attendance_status_id')
                    ->withDefault(['id'=>null, 'code'=>null]);
    }

    public function subjectAttendances() { return $this->hasMany(SubjectAttendance::class); }

    /*** Scopes & helpers ***/
    public function scopeForDate($q, $date)
    {
        return $q->whereDate('date', Carbon::parse($date)->toDateString());
    }

    public function isStudent(): bool
    {
        return in_array($this->attendable_type, [Student::class, 'student'], true);
    }

    public function isStaff(): bool
    {
        return in_array($this->attendable_type, [Staff::class, 'staff'], true);
    }

    public function getStatusCodeAttribute(): ?string
    {
        if ($this->attendance_status_id) return self::statusCodeById($this->attendance_status_id);
        return null;
    }

    private static function statusCodeById($id): ?string
    {
        if (!$id) return null;
        return AttendanceStatus::whereKey((int)$id)->value('code');
    }

    public static function legacyMonthlyCollection($attendances, int $attendeesType, array $entityMap = []): Collection
    {
        $rows = collect($attendances)
            ->groupBy(function ($attendance) {
                $date = Carbon::parse($attendance->date);

                return implode(':', [
                    $attendance->attendable_id,
                    $date->format('Y'),
                    $date->format('m'),
                ]);
            })
            ->map(function ($group) use ($attendeesType, $entityMap) {
                $first = $group->sortBy('date')->first();
                $date = Carbon::parse($first->date);
                $yearTitle = (int) $date->format('Y');
                $linkId = (int) $first->attendable_id;

                if (!array_key_exists($yearTitle, self::$legacyYearIds)) {
                    self::$legacyYearIds[$yearTitle] = Year::where('title', $yearTitle)->value('id');
                }

                $row = (object) [
                    'id' => $first->id,
                    'attendees_type' => $attendeesType,
                    'link_id' => $linkId,
                    'years_id' => self::$legacyYearIds[$yearTitle],
                    'months_id' => (int) $date->format('n'),
                    'status' => $first->status ?? 1,
                ];

                for ($day = 1; $day <= 32; $day++) {
                    $field = 'day_'.$day;
                    $row->{$field} = null;
                }

                foreach ($group as $attendance) {
                    $field = 'day_'.((int) Carbon::parse($attendance->date)->format('j'));
                    $row->{$field} = $attendance->attendance_status_id;
                }

                foreach (($entityMap[$linkId] ?? []) as $key => $value) {
                    $row->{$key} = $value;
                }

                return $row;
            })
            ->sortBy(function ($row) {
                return sprintf('%04d-%02d-%08d', $row->years_id ?: 0, $row->months_id ?: 0, $row->link_id ?: 0);
            })
            ->values();

        return $rows;
    }
}
