<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectAttendance extends BaseModel
{
    protected $fillable = [
        'date',
        'attendance_id',
        'student_id',
        'subject_id',
        'class_routine_detail_id',
        'attendance_status_id',
        'in_at',
        'out_at',
        'meta',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'date'   => 'date',
        'in_at'  => 'datetime',
        'out_at' => 'datetime',
        'meta'   => 'array',
    ];

    /** Parent daily attendance row (optional) */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /** Status relation — never null thanks to withDefault() */
    public function status(): BelongsTo
    {
        return $this->belongsTo(AttendanceStatus::class, 'attendance_status_id')
            ->withDefault(function ($s) {
                // Ensure $this->status->code is safe even if no row exists
                $s->code  = null;
                $s->label = null;
            });
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(ClassRoutineDetail::class, 'class_routine_detail_id');
    }

    /** Safe accessor for use everywhere (views, JSON, logs) */
    public function getStatusCodeAttribute(): ?string
    {
        if ($this->relationLoaded('status') && $this->status) {
            return $this->status->code; // null-safe due to withDefault()
        }

        if ($this->attendance_status_id) {
            return AttendanceStatus::whereKey($this->attendance_status_id)->value('code');
        }

        return null;
    }
}
