<?php
// app/Services/SubjectAttendanceService.php
namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Models\Student;

class SubjectAttendanceService
{
    /**
     * Mark all class routine slots that are "ongoing now" for the student's
     * faculty/semester/batch as Present (create if missing).
     *
     * This is resilient to column name differences via Schema::hasColumn checks.
     */
    public function markStudentPresentForNow(Student $student, Carbon $now): void
    {
        $date = $now->toDateString();

        if (!Schema::hasTable('class_routines') || !Schema::hasTable('subject_attendances')) {
            return;
        }

        // Resolve column names (fall back patterns)
        $facCol = Schema::hasColumn('class_routines','faculty_id') ? 'faculty_id' : (Schema::hasColumn('class_routines','faculty') ? 'faculty' : null);
        $semCol = Schema::hasColumn('class_routines','semester_id') ? 'semester_id' : (Schema::hasColumn('class_routines','semester') ? 'semester' : null);
        $batCol = Schema::hasColumn('class_routines','batch_id') ? 'batch_id' : (Schema::hasColumn('class_routines','batch') ? 'batch' : (Schema::hasColumn('class_routines','student_batch_id') ? 'student_batch_id' : null));
        $dayCol = Schema::hasColumn('class_routines','day_of_week') ? 'day_of_week' : (Schema::hasColumn('class_routines','day') ? 'day' : null);
        $stCol  = Schema::hasColumn('class_routines','start_time') ? 'start_time' : 'start';
        $enCol  = Schema::hasColumn('class_routines','end_time')   ? 'end_time'   : 'end';
        $subCol = Schema::hasColumn('class_routines','subject_id') ? 'subject_id' : 'subject';

        if (!$facCol || !$semCol || !$batCol || !$subCol) return;

        $dow = (int)$now->dayOfWeek; // 0=Sun ... 6=Sat
        // Some DBs store 1-7; create tolerant condition
        $routine = DB::table('class_routines')->where($facCol, $student->faculty)
            ->where($semCol, $student->semester)
            ->where($batCol, $student->batch);

        if ($dayCol) {
            $routine->where(function($w) use ($dayCol, $dow) {
                $w->where($dayCol, $dow)->orWhere($dayCol, $dow === 0 ? 7 : $dow); // accept 0 or 7 for Sunday
            });
        }

        // time window: slot contains "now"
        if (Schema::hasColumn('class_routines', $stCol) && Schema::hasColumn('class_routines', $enCol)) {
            $t = $now->format('H:i:s');
            $routine->where($stCol, '<=', $t)->where($enCol, '>=', $t);
        }

        $slots = $routine->select('id', $subCol.' as subject_id')->get();
        if ($slots->isEmpty()) return;

        foreach ($slots as $slot) {
            // upsert subject_attendances
            $exists = DB::table('subject_attendances')->where([
                'students_id' => $student->id,
                'subject_id'  => $slot->subject_id,
                'date'        => $date,
            ])->first();

            if ($exists) {
                // keep as present; do not downgrade here
                continue;
            }

            DB::table('subject_attendances')->insert([
                'students_id' => $student->id,
                'subject_id'  => $slot->subject_id,
                'date'        => $date,
                'status_code' => 'P', // or attendance_status_id if your schema uses numeric
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ]);
        }
    }
}
