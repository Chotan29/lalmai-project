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

    public static function lastPrintDateForStudents(array $studentIds, array $examParams, string $dateFrom = null, string $dateTo = null): array
    {
        $query = static::selectRaw('student_id, MAX(print_date) as last_print_date')
            ->whereIn('student_id', $studentIds);

        if (!empty($examParams['years_id']))     $query->where('years_id',     $examParams['years_id']);
        if (!empty($examParams['months_id']))    $query->where('months_id',    $examParams['months_id']);
        if (!empty($examParams['exams_id']))     $query->where('exams_id',     $examParams['exams_id']);
        if (!empty($examParams['faculty_id']))   $query->where('faculty_id',   $examParams['faculty_id']);
        if (!empty($examParams['semesters_id'])) $query->where('semesters_id', $examParams['semesters_id']);

        if ($dateFrom && $dateTo) {
            $query->whereBetween('print_date', [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            $query->where('print_date', '>=', $dateFrom);
        } elseif ($dateTo) {
            $query->where('print_date', '<=', $dateTo);
        }

        return $query->groupBy('student_id')
            ->pluck('last_print_date', 'student_id')
            ->toArray();
    }
}
