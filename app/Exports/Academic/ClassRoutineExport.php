<?php

namespace App\Exports\Academic;

use App\Models\ClassRoutine;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClassRoutineExport implements FromArray, WithHeadings
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        // Insert teacher_name immediately after teacher_identifier,
        // and also include teacher_reg_no as a separate column
        return [
            'department',
            'faculty',
            'semester',
            'batch_title',
            'subject_code',
            'subject_title',
            'teacher_identifier', // now equals staff.reg_no
            'teacher_name',       // new
            //'teacher_reg_no',     // explicit reg_no column (may duplicate identifier if you want)
            'day_of_week',
            'start_time',
            'end_time',
            'room_number',
            'period',
            'status',
        ];
    }

    public function array(): array
    {
        $q = ClassRoutine::with([
            'department:id,department',
            'faculty:id,faculty',
            'semester:id,semester',
            'batch:id,title',
            'subject:id,title,code',
            // only columns that exist in your staff table
            'teacher:id,reg_no,first_name,middle_name,last_name,email',
        ]);

        foreach ([
            'department_id',
            'faculty_id',
            'semester_id',
            'student_batch_id',
            'subject_id',
            'teacher_id',
            'day_of_week',
            'status',
        ] as $f) {
            if (!empty($this->filters[$f])) {
                $q->where($f, $this->filters[$f]);
            }
        }

        $rows = $q->orderBy('day_of_week')->orderBy('start_time')->get();

        $out = [];
        foreach ($rows as $r) {
            // teacher_identifier should be reg_no; if missing, fall back gracefully
            $teacherRegNo = $r->teacher->reg_no ?? null;
            $teacherIdentifier = $teacherRegNo ?: ($r->teacher->email ?? (string)($r->teacher->id ?? ''));

            // build full name safely
            $names = array_filter([
                $r->teacher->first_name ?? null,
                $r->teacher->middle_name ?? null,
                $r->teacher->last_name ?? null,
            ]);
            $teacherName = trim(implode(' ', $names));

            // times: handle both "HH:MM" strings and full timestamps
            $start = (string)($r->start_time ?? '');
            $end   = (string)($r->end_time ?? '');
            $start = strlen($start) >= 5 ? substr($start, 0, 5) : $start;
            $end   = strlen($end)   >= 5 ? substr($end, 0, 5)   : $end;

            $out[] = [
                $r->department->department ?? '',
                $r->faculty->faculty ?? '',
                $r->semester->semester ?? '',
                $r->batch->title ?? '',
                $r->subject->code ?? '',
                $r->subject->title ?? '',
                $teacherIdentifier,     // teacher_identifier (now equals reg_no when present)
                $teacherName,           // teacher_name (new)
                //$teacherRegNo ?? '',    // teacher_reg_no (explicit)
                $r->day_of_week ?? '',
                $start,
                $end,
                $r->room_number ?? '',
                $r->period ?? '',
                (int)($r->status ?? 0),
            ];
        }

        return $out;
    }
}
