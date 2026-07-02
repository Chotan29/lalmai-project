<?php

namespace App\Exports\Academic;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClassRoutineTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'department',
            'faculty',
            'semester',
            'batch_title',
            'subject_code',
            'teacher_reg_no', // staff.reg_no (primary)
            'teacher_name',       // optional, informational
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
        // sample rows (safe to remove)
        return [[
            'Business Administration',
            'Bachelor of Business Administration (BBA)',
            '10TH SEMESTER (BBA)',
            'SPRING 2025',
            '71103',
            '1111',          // reg_no
            'John Doe',
            'Friday',
            '10:00',
            '10:45',
            '88166',
            '4th',
            1,
        ]];
    }
}
