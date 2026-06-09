<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmitCardPrintLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'student_id', 'years_id', 'months_id', 'exams_id',
        'faculty_id', 'semesters_id', 'print_type',
        'printed_by', 'print_date', 'printed_at',
    ];

    protected $dates = ['printed_at'];

    public static function logPrint(array $studentIds, array $examParams, int $printType)
    {
        $now   = now();
        $today = $now->toDateString();
        $by    = auth()->id();

        $rows = [];
        foreach ($studentIds as $sid) {
            $rows[] = [
                'student_id'   => $sid,
                'years_id'     => $examParams['years_id'],
                'months_id'    => $examParams['months_id'],
                'exams_id'     => $examParams['exams_id'],
                'faculty_id'   => $examParams['faculty_id'],
                'semesters_id' => $examParams['semesters_id'],
                'print_type'   => $printType,
                'printed_by'   => $by,
                'print_date'   => $today,
                'printed_at'   => $now,
            ];
        }

        if (!empty($rows)) {
            static::insert($rows);
        }
    }

    public static function lastPrintDateForStudents(array $studentIds, array $examParams): array
    {
        return static::selectRaw('student_id, MAX(print_date) as last_print_date, MAX(printed_at) as last_printed_at')
            ->where([
                ['years_id',     $examParams['years_id']],
                ['months_id',    $examParams['months_id']],
                ['exams_id',     $examParams['exams_id']],
                ['faculty_id',   $examParams['faculty_id']],
                ['semesters_id', $examParams['semesters_id']],
            ])
            ->whereIn('student_id', $studentIds)
            ->groupBy('student_id')
            ->pluck('last_print_date', 'student_id')
            ->toArray();
    }
}
