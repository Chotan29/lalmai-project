<?php

namespace App\Observers;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\SubjectAttendance;

class AttendanceObserver
{
    public function created(Attendance $att)
    {
        $this->maybePropagate($att);
    }

    public function updated(Attendance $att)
    {
        $this->maybePropagate($att);
    }

    protected function maybePropagate(Attendance $att)
    {
        // Only students, only for "today", only if Present
        if (!$att->isStudent()) return;

        $today = Carbon::today()->toDateString();
        if ($att->date->toDateString() !== $today) return;

        // Use getRelation() to avoid shadowing by the 'status' tinyint column on attendances table
        $status = $att->relationLoaded('status') ? $att->getRelation('status') : AttendanceStatus::find($att->attendance_status_id);
        if (!$status || strtoupper($status->code) !== 'P') return;

        $student = $att->attendable;
        if (!$student) return;

        $slots = $student->todaysSlots();
        if ($slots->isEmpty()) return;

        $stPresentId = AttendanceStatus::where('code','P')->value('id');

        foreach ($slots as $slot) {
            SubjectAttendance::updateOrCreate(
                [
                    'date'       => $today,
                    'student_id' => $student->id,
                    'subject_id' => $slot->subject_id,
                ],
                [
                    'attendance_id'         => $att->id,
                    'class_routine_detail_id'=> $slot->id,
                    'attendance_status_id'  => $stPresentId,
                    'in_at'                 => $att->check_in_at ?: null,
                    'out_at'                => $att->check_out_at ?: null,
                ]
            );
        }
    }
}
