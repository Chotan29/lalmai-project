<?php

namespace App\Imports\Academic;

use App\Models\ClassRoutine;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\StudentBatch;
use App\Models\Subject;
use App\Models\Staff;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClassRoutineImport implements ToCollection, WithHeadingRow
{
    protected string $mode;             // insert|upsert|replace  (replace is handled by controller pre-delete)
    protected string $uniqueBy;         // composite|subject_teacher_day_time
    protected int    $userId;

    protected array $summary = [
        'inserted'     => 0,
        'updated'      => 0,
        'skipped'      => 0,
        'errors_count' => 0,
        'errors'       => [],
    ];

    /**
     * @param string $mode 'insert'|'upsert'|'replace'
     * @param string $uniqueBy 'composite'|'subject_teacher_day_time'
     * @param int    $userId
     */
    public function __construct(string $mode = 'insert', string $uniqueBy = 'composite', int $userId = 0)
    {
        $this->mode     = $mode;
        $this->uniqueBy = $uniqueBy;
        $this->userId   = $userId;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
        // Normalize all string keys to snake_case; WithHeadingRow already does this
        // but users sometimes upload sheets with odd caps/spaces.
        DB::beginTransaction();

        try {
            foreach ($rows as $idx => $row) {
                try {
                    $r = $this->normalizeRow($row->toArray());

                    // Basic presence checks
                    $required = ['department','faculty','semester','batch_title','subject_code','day_of_week','start_time','end_time'];
                    foreach ($required as $col) {
                        if (empty($r[$col])) {
                            throw new \RuntimeException("Missing required column: {$col}");
                        }
                    }

                    // Resolve IDs
                    $department = Department::where('department', $r['department'])->first();
                    if (!$department) {
                        throw new \RuntimeException("Department not found: {$r['department']}");
                    }

                    $faculty = Faculty::where('faculty', $r['faculty'])->first();
                    if (!$faculty) {
                        throw new \RuntimeException("Faculty not found: {$r['faculty']}");
                    }

                    $semester = Semester::where('semester', $r['semester'])->first();
                    if (!$semester) {
                        throw new \RuntimeException("Semester not found: {$r['semester']}");
                    }

                    $batch = StudentBatch::where('title', $r['batch_title'])->first();
                    if (!$batch) {
                        throw new \RuntimeException("Batch not found: {$r['batch_title']}");
                    }

                    $subject = Subject::where('code', $r['subject_code'])->first();
                    if (!$subject) {
                        throw new \RuntimeException("Subject not found (code): {$r['subject_code']}");
                    }

                    // Teacher: prefer teacher_reg_no, then teacher_identifier (both intended to be staff.reg_no)
                    $teacherKey = $r['teacher_reg_no'] ?: $r['teacher_identifier'];
                    $teacher    = null;

                    if ($teacherKey) {
                        $teacher = Staff::where('reg_no', $teacherKey)->first();
                        if (!$teacher) {
                            // Very soft fallback: try email or numeric id if someone uploads that by mistake
                            $teacher = Staff::where('email', $teacherKey)->first();
                            if (!$teacher && ctype_digit((string)$teacherKey)) {
                                $teacher = Staff::where('id', (int)$teacherKey)->first();
                            }
                        }
                    }

                    if (!$teacher) {
                        throw new \RuntimeException("Teacher not found by reg_no/identifier: ".($teacherKey ?: '[empty]'));
                    }

                    // Normalize times (expect HH:MM 24h)
                    $start = $this->hhmm($r['start_time']);
                    $end   = $this->hhmm($r['end_time']);
                    if (!$start || !$end) {
                        throw new \RuntimeException("Invalid time (use HH:MM 24h). Got start={$r['start_time']} end={$r['end_time']}");
                    }

                    $payload = [
                        'department_id'    => $department->id,
                        'faculty_id'       => $faculty->id,
                        'semester_id'      => $semester->id,
                        'student_batch_id' => $batch->id,
                        'subject_id'       => $subject->id,
                        'teacher_id'       => $teacher->id,
                        'day_of_week'      => $this->normalizeDay($r['day_of_week']),
                        'start_time'       => $start,
                        'end_time'         => $end,
                        'room_number'      => (string) ($r['room_number'] ?? ''),
                        'period'           => $r['period'] ?? null,
                        'status'           => isset($r['status']) && (string)$r['status'] !== '' ? (int)$r['status'] : 1,
                    ];

                    // Upsert behavior
                    $existing = $this->findExisting($payload);

                    if ($existing) {
                        if ($this->mode === 'insert') {
                            // Skip if insert-only
                            $this->summary['skipped']++;
                            continue;
                        }

                        $existing->fill($payload);
                        $existing->last_updated_by = $this->userId ?: $existing->last_updated_by;
                        $existing->save();

                        $this->summary['updated']++;
                    } else {
                        $payload['created_by'] = $this->userId ?: 1;
                        ClassRoutine::create($payload);
                        $this->summary['inserted']++;
                    }

                } catch (\Throwable $rowEx) {
                    $this->summary['errors_count']++;
                    $this->summary['skipped']++;
                    $this->summary['errors'][] = 'Row '.($idx + 2).': '.$rowEx->getMessage(); // +2 (1 for heading, 1 for 1-based)
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->summary['errors_count']++;
            $this->summary['errors'][] = 'Fatal import error: '.$e->getMessage();
        }
    }

    /**
     * Unique match rules.
     */
    protected function findExisting(array $p): ?ClassRoutine
    {
        $q = ClassRoutine::query();

        if ($this->uniqueBy === 'subject_teacher_day_time') {
            $q->where('subject_id', $p['subject_id'])
              ->where('teacher_id', $p['teacher_id'])
              ->where('day_of_week', $p['day_of_week'])
              ->where('start_time', $p['start_time'])
              ->where('end_time', $p['end_time']);
        } else {
            // 'composite' default
            $q->where('department_id',    $p['department_id'])
              ->where('faculty_id',       $p['faculty_id'])
              ->where('semester_id',      $p['semester_id'])
              ->where('student_batch_id', $p['student_batch_id'])
              ->where('subject_id',       $p['subject_id'])
              ->where('day_of_week',      $p['day_of_week'])
              ->where('start_time',       $p['start_time'])
              ->where('end_time',         $p['end_time']);
        }

        return $q->first();
    }

    /**
     * Normalize row keys and values.
     */
    protected function normalizeRow(array $row): array
    {
        $n = [];
        foreach ($row as $k => $v) {
            $key = Str::snake(trim((string)$k));
            $n[$key] = is_string($v) ? trim($v) : $v;
        }

        // Alias support for teacher identifiers
        $n['teacher_identifier'] = $n['teacher_identifier'] ?? $n['teacher_reg_no'] ?? $n['teacher_code'] ?? $n['teacher_id'] ?? null;
        $n['teacher_reg_no']     = $n['teacher_reg_no']     ?? $n['teacher_identifier'] ?? null;

        return $n;
    }

    /**
     * Normalize weekday capitalization.
     */
    protected function normalizeDay(?string $v): string
    {
        $map = [
            'saturday'  => 'Saturday',
            'sunday'    => 'Sunday',
            'monday'    => 'Monday',
            'tuesday'   => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday'  => 'Thursday',
            'friday'    => 'Friday',
        ];
        $k = strtolower((string)$v);
        return $map[$k] ?? ($v ?: 'Monday');
    }

    /**
     * Accept “HH:MM”, “H:MM”, “HH:MM:SS” and coerce to "HH:MM".
     */
    protected function hhmm($v): ?string
    {
        if ($v === null || $v === '') return null;

        $s = trim((string)$v);

        // If the cell was numeric time in Excel, it might come as "10:00:00" or "10:00"
        // Try to parse with regex
        if (preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $s, $m)) {
            $h = (int)$m[1];
            $i = (int)$m[2];
            if ($h >= 0 && $h <= 23 && $i >= 0 && $i <= 59) {
                return sprintf('%02d:%02d', $h, $i);
            }
        }

        return null;
    }

    /**
     * Called by controller after Excel::import() to show a summary.
     */
    public function summary(): array
    {
        return $this->summary;
    }
}
