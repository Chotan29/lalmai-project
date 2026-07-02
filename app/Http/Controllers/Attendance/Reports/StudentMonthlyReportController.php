<?php

namespace App\Http\Controllers\Attendance\Reports;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class StudentMonthlyReportController extends CollegeBaseController
{
    protected $base_route = 'attendance.reports.students.monthly';
    protected $view_path  = 'attendance.reports.students';
    protected $panel      = 'Student Monthly Attendance Report';

    /* ==========================================================
       Page
       ========================================================== */
    public function index(Request $request)
    {
        try {
            $data = [
                'panel'            => $this->panel,
                'department_heads' => $this->departmentHeads(),
                'years'            => $this->yearOptions(5),
                'months'           => $this->monthOptions(),
            ];
            return view(parent::loadDataToView($this->view_path.'.index'), $data);
        } catch (\Throwable $e) {
            Log::error('StudentMonthlyReport@index failed', ['err'=>$e->getMessage()]);
            return back()->with('message_danger', 'Failed to load Student Monthly Attendance Report.');
        }
    }

    /* ==========================================================
       JSON data for the grid
       ========================================================== */
    public function data(Request $request)
    {
        try {
            $filters = $request->only([
                'department_head_id','department_id','faculty_id','semester_id',
                'student_batch_id','subject_id','year','month'
            ]);

            $year       = (int)($filters['year'] ?? 0);
            $month      = (int)($filters['month'] ?? 0);
            $semesterId = (int)($filters['semester_id'] ?? 0);
            $batchId    = (int)($filters['student_batch_id'] ?? 0);
            $subjectId  = (int)($filters['subject_id'] ?? 0);

            // ✅ REQUIRE: Year + Month + Semester + Batch
            if (!$year || !$month || !$semesterId || !$batchId) {
                return response()->json([
                    'ok'  => false,
                    'msg' => 'Pick hierarchy (Semester & Batch), Year and Month.',
                    'rows'=> []
                ]);
            }

            // Period
            $start = Carbon::create($year, $month, 1)->startOfDay();
            $end   = $start->copy()->endOfMonth()->endOfDay();
            $daysInMonth = $start->daysInMonth;

            // Students under hierarchy
            $studentsQ = DB::table('students')->select('id');
            $this->applyHierarchyToStudents($studentsQ, $filters);
            $studentIds = $studentsQ->pluck('id')->all();

            if (!$studentIds) {
                return response()->json([
                    'ok'   => true,
                    'type' => $subjectId ? 'Subject Attendance' : 'Regular Attendance',
                    'days' => range(1, $daysInMonth),
                    'rows' => [],
                    'meta' => ['total_students' => 0],
                ]);
            }

            $stuInfo = $this->studentInfo($studentIds);

            $meta = ['total_students' => count($studentIds)];

            if ($subjectId) {
                // ===== SUBJECT ATTENDANCE =====
                $map  = $this->fetchSubjectAttendance($studentIds, $subjectId, $semesterId, $batchId, $start, $end);
                $type = 'Subject Attendance';

                // Teacher/Instructor from Class Routine
                $teacher = $this->findTeacherForSubject($subjectId, $semesterId, $batchId);
                if ($teacher) $meta['teacher'] = $teacher;

            } else {
                // ===== REGULAR ATTENDANCE (polymorphic) =====
                $map  = $this->fetchRegularAttendance($studentIds, $start, $end);
                $type = 'Regular Attendance';
            }

            // Build rows
            $days = range(1, $daysInMonth);
            $rows = [];
            foreach ($studentIds as $sid) {
                $info = $stuInfo[$sid] ?? ['reg_no'=>'—','name'=>'—'];
                $row  = [
                    'student_id' => $sid,
                    'reg_no'     => $info['reg_no'],
                    'name'       => $info['name'],
                    'd'          => [],
                    'count'      => ['P'=>0,'A'=>0,'L'=>0,'H'=>0,'LV'=>0,'EL'=>0],
                ];
                foreach ($days as $d) {
                    $key  = $start->copy()->day($d)->toDateString();
                    $code = isset($map[$sid][$key]) ? $map[$sid][$key] : '';
                    $row['d'][$d] = $code;
                    if ($code && isset($row['count'][$code])) $row['count'][$code]++;
                }
                $rows[] = $row;
            }

            return response()->json([
                'ok'   => true,
                'type' => $type,
                'days' => $days,
                'rows' => $rows,
                'meta' => $meta,
            ]);

        } catch (\Throwable $e) {
            Log::error('StudentMonthlyReport@data failed', [
                'err'=>$e->getMessage(), 'line'=>$e->getLine(), 'file'=>$e->getFile(),
                'req'=>$request->all(),
            ]);
            return response()->json([
                'ok'  => false,
                'msg' => 'Failed to load Student Monthly Attendance Report.',
                'rows'=> []
            ], 200);
        }
    }

    /* ==========================================================
       Regular attendance (polymorphic attendances)
       ========================================================== */
    private function fetchRegularAttendance(array $studentIds, Carbon $start, Carbon $end): array
    {
        $attTable = null;
        foreach (['attendances','attendance','attendence'] as $t) {
            if (Schema::hasTable($t)) { $attTable = $t; break; }
        }
        if (!$attTable) return [];

        foreach (['date','attendable_id','attendable_type','attendance_status_id'] as $col) {
            if (!Schema::hasColumn($attTable, $col)) return [];
        }

        // morph types for Student
        $studentTypes = $this->studentMorphTypes();

        // status join
        $statusTable = Schema::hasTable('attendance_statuses') ? 'attendance_statuses' : null;
        $statusIdCol = $statusTable ? (Schema::hasColumn($statusTable, 'id') ? 'id' : null) : null;
        $statusCode  = $statusTable ? ($this->firstExistingColumn($statusTable, ['code','short_code','symbol','abbr','title','name','status'])) : null;

        $q = DB::table($attTable)
            ->select("{$attTable}.attendable_id as sid", DB::raw("DATE(`{$attTable}`.`date`) as d"))
            ->whereIn("{$attTable}.attendable_id", $studentIds)
            ->whereIn("{$attTable}.attendable_type", $studentTypes)
            ->whereDate("{$attTable}.date", '>=', $start->toDateString())
            ->whereDate("{$attTable}.date", '<=', $end->toDateString());

        if ($statusTable && $statusIdCol && $statusCode) {
            $q->leftJoin($statusTable, "{$statusTable}.{$statusIdCol}", '=', "{$attTable}.attendance_status_id")
              ->addSelect(DB::raw("COALESCE(`{$statusTable}`.`{$statusCode}`, '') as s"));
        } else {
            $q->addSelect("{$attTable}.attendance_status_id as s");
        }

        $rows = $q->get();
        $map  = [];
        foreach ($rows as $r) {
            $map[$r->sid][$r->d] = $this->normalizeStatus($r->s);
        }
        return $map;
    }

    private function studentMorphTypes(): array
    {
        $cands = [
            'App\\Models\\Student',
            'App\\Student',
            'student',
            'Student',
        ];
        if (class_exists('App\\Models\\Student')) array_unshift($cands, 'App\\Models\\Student');
        elseif (class_exists('App\\Student'))     array_unshift($cands, 'App\\Student');
        return array_values(array_unique($cands));
    }

    /* ==========================================================
       Subject attendance + Teacher
       ========================================================== */
    private function fetchSubjectAttendance(array $studentIds, int $subjectId, int $semesterId, int $batchId, Carbon $start, Carbon $end): array
    {
        $table = null;
        foreach (['subject_attendances','attendance_subjects','student_subject_attendances','semester_subject_attendances'] as $t) {
            if (Schema::hasTable($t)) { $table = $t; break; }
        }
        if (!$table) return [];

        foreach (['date','student_id','subject_id','attendance_status_id'] as $col) {
            if (!Schema::hasColumn($table, $col)) return [];
        }

        $statusTable = Schema::hasTable('attendance_statuses') ? 'attendance_statuses' : null;
        $statusIdCol = $statusTable ? (Schema::hasColumn($statusTable, 'id') ? 'id' : null) : null;
        $statusCode  = $statusTable ? ($this->firstExistingColumn($statusTable, ['code','short_code','symbol','abbr','title','name','status'])) : null;

        $q = DB::table($table)
            ->select("{$table}.student_id as sid", DB::raw("DATE(`{$table}`.`date`) as d"))
            ->whereIn("{$table}.student_id", $studentIds)
            ->where("{$table}.subject_id", $subjectId)
            ->whereDate("{$table}.date", '>=', $start->toDateString())
            ->whereDate("{$table}.date", '<=', $end->toDateString());

        // If detail holds semester/batch, filter further (safe conditional)
        if (Schema::hasColumn($table, 'semester_id'))      $q->where("{$table}.semester_id", $semesterId);
        if (Schema::hasColumn($table, 'student_batch_id')) $q->where("{$table}.student_batch_id", $batchId);

        if ($statusTable && $statusIdCol && $statusCode) {
            $q->leftJoin($statusTable, "{$statusTable}.{$statusIdCol}", '=', "{$table}.attendance_status_id")
              ->addSelect(DB::raw("COALESCE(`{$statusTable}`.`{$statusCode}`, '') as s"));
        } else {
            $q->addSelect("{$table}.attendance_status_id as s");
        }

        $rows = $q->get();
        $map  = [];
        foreach ($rows as $r) {
            $map[$r->sid][$r->d] = $this->normalizeStatus($r->s);
        }
        return $map;
    }

    /**
     * Try to find the Teacher/Instructor for a given subject + (semester,batch).
     * Looks into class_routine_details (preferred), falling back to class_routines.
     * Supports multiple possible staff id/name/designation columns.
     */
    private function findTeacherForSubject(int $subjectId, int $semesterId, int $batchId): ?string
    {
        // ===== 1) Prefer class_routine_details (has per-slot staff) =====
        $detailTab = $this->firstExistingTable(['class_routine_details','class_routine_detail','routine_details']);
        $masterTab = $this->firstExistingTable(['class_routines','class_routine','routines']);
        if ($detailTab) {
            $detailStaff = $this->firstExistingColumn($detailTab, ['staff_id','teacher_id','lecturer_id','instructor_id','employee_id','user_id']);
            $detailSubj  = $this->firstExistingColumn($detailTab, ['subject_id','subjects_id']);
            $detailClassFk = $this->firstExistingColumn($detailTab, ['class_routine_id','routine_id','master_id']);

            if ($detailStaff) {
                $q = DB::table($detailTab)->select("{$detailTab}.{$detailStaff} as staff_id");

                if ($detailSubj) $q->where("{$detailTab}.{$detailSubj}", $subjectId);

                // If subject not on detail, try join to master for subject/semester/batch
                if (!$detailSubj && $masterTab && $detailClassFk) {
                    $mId = $this->firstExistingColumn($masterTab, ['id','class_routine_id']);
                    if ($mId) {
                        $q->join($masterTab, "{$masterTab}.{$mId}", '=', "{$detailTab}.{$detailClassFk}");
                        $mSubj = $this->firstExistingColumn($masterTab, ['subject_id','subjects_id','semester_subject_id']);
                        if ($mSubj) $q->where("{$masterTab}.{$mSubj}", $subjectId);
                        $mSem  = $this->firstExistingColumn($masterTab, ['semester_id','sem_id']);
                        if ($mSem)  $q->where("{$masterTab}.{$mSem}", $semesterId);
                        $mBatch= $this->firstExistingColumn($masterTab, ['student_batch_id','batch_id']);
                        if ($mBatch) $q->where("{$masterTab}.{$mBatch}", $batchId);
                    }
                } else {
                    // Filter by sem/batch if they exist on detail
                    $dSem  = $this->firstExistingColumn($detailTab, ['semester_id','sem_id']);
                    if ($dSem)  $q->where("{$detailTab}.{$dSem}", $semesterId);
                    $dBatch= $this->firstExistingColumn($detailTab, ['student_batch_id','batch_id']);
                    if ($dBatch) $q->where("{$detailTab}.{$dBatch}", $batchId);
                }

                // Use most frequent staff_id as the teacher
                $row = DB::query()->fromSub(
                    $q->whereNotNull("{$detailTab}.{$detailStaff}")
                      ->where("{$detailTab}.{$detailStaff}", '>', 0),
                    't'
                )->select('staff_id', DB::raw('COUNT(*) as c'))
                 ->groupBy('staff_id')->orderByDesc('c')->first();

                if ($row && $row->staff_id) {
                    $disp = $this->staffDisplayById((int)$row->staff_id);
                    if ($disp) return $disp;
                }
            }
        }

        // ===== 2) Fallback to class_routines (subject assigned at master) =====
        if ($masterTab) {
            $mStaff = $this->firstExistingColumn($masterTab, ['staff_id','teacher_id','lecturer_id','instructor_id','employee_id','user_id']);
            $mSubj  = $this->firstExistingColumn($masterTab, ['subject_id','subjects_id','semester_subject_id']);
            if ($mStaff && $mSubj) {
                $q = DB::table($masterTab)->select("{$masterTab}.{$mStaff} as staff_id")
                    ->where("{$masterTab}.{$mSubj}", $subjectId);

                $mSem  = $this->firstExistingColumn($masterTab, ['semester_id','sem_id']);
                if ($mSem)  $q->where("{$masterTab}.{$mSem}", $semesterId);
                $mBatch= $this->firstExistingColumn($masterTab, ['student_batch_id','batch_id']);
                if ($mBatch) $q->where("{$masterTab}.{$mBatch}", $batchId);

                $row = $q->whereNotNull("{$masterTab}.{$mStaff}")
                         ->where("{$masterTab}.{$mStaff}", '>', 0)
                         ->first();

                if ($row && $row->staff_id) {
                    $disp = $this->staffDisplayById((int)$row->staff_id);
                    if ($disp) return $disp;
                }
            }
        }

        return null;
    }

    /**
     * Resolve staff name + designation by id.
     * Supports: staff table + staff_designations table (name/title/designation).
     */
    private function staffDisplayById(int $staffId): ?string
    {
        $staffTable = $this->firstExistingTable(['staff','employees','users']);
        if (!$staffTable) return null;

        $idCol = Schema::hasColumn($staffTable, 'id') ? 'id' : null;
        if (!$idCol) return null;

        $first  = $this->firstExistingColumn($staffTable, ['first_name','firstname','given_name']);
        $middle = $this->firstExistingColumn($staffTable, ['middle_name','middlename']);
        $last   = $this->firstExistingColumn($staffTable, ['last_name','lastname','surname','family_name']);
        $name   = $this->firstExistingColumn($staffTable, ['name','full_name','display_name']);
        $desigFk= $this->firstExistingColumn($staffTable, ['staff_designation_id','designation_id','role_id']);

        $sel = ["{$staffTable}.{$idCol}"];
        if ($name)   $sel[] = "{$staffTable}.{$name} as st_name";
        if ($first)  $sel[] = "{$staffTable}.{$first} as st_first";
        if ($middle) $sel[] = "{$staffTable}.{$middle} as st_middle";
        if ($last)   $sel[] = "{$staffTable}.{$last} as st_last";
        if ($desigFk)$sel[] = "{$staffTable}.{$desigFk} as st_desig_id";

        $staff = DB::table($staffTable)->select($sel)->where("{$staffTable}.{$idCol}", $staffId)->first();
        if (!$staff) return null;

        // build name
        $display = '';
        if ($name && !empty($staff->st_name)) $display = $staff->st_name;
        else {
            $parts = [];
            if ($first && !empty($staff->st_first))   $parts[] = trim((string)$staff->st_first);
            if ($middle && !empty($staff->st_middle)) $parts[] = trim((string)$staff->st_middle);
            if ($last && !empty($staff->st_last))     $parts[] = trim((string)$staff->st_last);
            $display = $parts ? implode(' ', $parts) : '';
        }

        // designation (optional)
        $desig = null;
        if ($desigFk && !empty($staff->st_desig_id)) {
            $desTab = $this->firstExistingTable(['staff_designations','designations','roles']);
            if ($desTab) {
                $desName = $this->firstExistingColumn($desTab, ['name','title','designation']);
                $desId   = $this->firstExistingColumn($desTab, ['id','designation_id','role_id']);
                if ($desName && $desId) {
                    $d = DB::table($desTab)->select($desName.' as dname')->where($desId,$staff->st_desig_id)->first();
                    if ($d && $d->dname) $desig = $d->dname;
                }
            }
        }

        if ($display && $desig) return $display.' ('.$desig.')';
        return $display ?: null;
    }

    /* ==========================================================
       Common helpers
       ========================================================== */
    private function departmentHeads()
    {
        if (!Schema::hasTable('department_heads')) return collect();
        $nameCol = Schema::hasColumn('department_heads','department_head') ? 'department_head'
                 : (Schema::hasColumn('department_heads','name') ? 'name' : null);
        if (!$nameCol) return collect();
        return DB::table('department_heads')
            ->when(Schema::hasColumn('department_heads','status'), fn($q)=>$q->where('status',1))
            ->orderBy($nameCol)->pluck($nameCol,'id');
    }

    private function yearOptions($back = 5)
    {
        $y = (int) date('Y'); $out=[];
        for ($i=0;$i<$back;$i++) $out[] = $y-$i;
        return $out;
    }

    private function monthOptions()
    {
        $out=[]; for($m=1;$m<=12;$m++) $out[$m] = date('F', mktime(0,0,0,$m,10));
        return $out;
    }

    private function studentsSynonym($wanted)
    {
        $map = [
            'faculty'           => ['faculty','faculty_id','program_id'],
            'semester'          => ['semester','semester_id','sem_id'],
            'student_batch_id'  => ['batch','student_batch_id','batch_id'],
            'department_id'     => ['department_id','dept_id'],
        ];
        $cand = $map[$wanted] ?? [$wanted];
        foreach ($cand as $c) if (Schema::hasColumn('students',$c)) return $c;
        return null;
    }

    private function applyHierarchyToStudents($q, $filters)
    {
        if (!empty($filters['department_id']) && Schema::hasColumn('students','department_id')) {
            $q->where('students.department_id', $filters['department_id']);
        }
        $stFac = $this->studentsSynonym('faculty');
        if (!empty($filters['faculty_id']) && $stFac) {
            $q->where('students.'.$stFac, $filters['faculty_id']);
        }
        $stSem = $this->studentsSynonym('semester');
        if (!empty($filters['semester_id']) && $stSem) {
            $q->where('students.'.$stSem, $filters['semester_id']);
        }
        $stBatch = $this->studentsSynonym('student_batch_id');
        if (!empty($filters['student_batch_id']) && $stBatch) {
            $q->where('students.'.$stBatch, $filters['student_batch_id']);
        }
    }

    private function studentInfo(array $ids)
    {
        $out = []; if (!$ids) return $out;

        $reg = null;
        foreach (['reg_no','reg_number','registration_no','register_no','regd_no','enrollment_no','enroll_no','roll_no','student_code'] as $c) {
            if (Schema::hasColumn('students',$c)) { $reg = $c; break; }
        }
        $name   = Schema::hasColumn('students','name') ? 'name' : null;
        $first  = Schema::hasColumn('students','first_name')  ? 'first_name'  : null;
        $middle = Schema::hasColumn('students','middle_name') ? 'middle_name' : null;
        $last   = Schema::hasColumn('students','last_name')   ? 'last_name'   : null;

        $sel = ['id'];
        if ($reg)   $sel[] = $reg.' as reg_no';
        if ($name)  $sel[] = $name.' as st_name';
        if ($first) $sel[] = $first.' as st_first';
        if ($middle)$sel[] = $middle.' as st_middle';
        if ($last)  $sel[] = $last.' as st_last';

        $rows = DB::table('students')->select($sel)->whereIn('id',$ids)->get();
        foreach ($rows as $r) {
            $display = '';
            if ($name && !empty($r->st_name)) $display = $r->st_name;
            else {
                $parts = [];
                if ($first && !empty($r->st_first))   $parts[] = trim((string)$r->st_first);
                if ($middle && !empty($r->st_middle)) $parts[] = trim((string)$r->st_middle);
                if ($last && !empty($r->st_last))     $parts[] = trim((string)$r->st_last);
                $display = $parts ? implode(' ', $parts) : '—';
            }
            $out[$r->id] = [
                'reg_no' => $reg ? ($r->reg_no ?: '—') : '—',
                'name'   => $display,
            ];
        }
        return $out;
    }

    private function normalizeStatus($v)
    {
        $t = strtoupper(trim((string)$v));
        if ($t === 'P' || strpos($t,'PRESENT') !== false) return 'P';
        if ($t === 'A' || strpos($t,'ABSENT')  !== false) return 'A';
        if ($t === 'L' || strpos($t,'LATE')    !== false) return 'L';
        if ($t === 'LV' || strpos($t,'LEAVE')  !== false) return 'LV';
        if ($t === 'EL') return 'EL';
        if ($t === 'H' || strpos($t,'HOLIDAY') !== false) return 'H';
        if ($t === '1' || $t === 'TRUE') return 'P';
        if ($t === '0' || $t === 'FALSE') return 'A';
        return '';
    }

    private function firstExistingColumn(string $table, array $cands)
    {
        foreach ($cands as $c) if (Schema::hasColumn($table,$c)) return $c;
        return null;
    }
    private function firstExistingTable(array $names)
    {
        foreach ($names as $n) if (Schema::hasTable($n)) return $n;
        return null;
    }

    private function statusPalette(): array
    {
        // From DB (preferred)
        if (Schema::hasTable('attendance_statuses')) {
            $rows = DB::table('attendance_statuses')
                ->select(['code','label','color','order'])
                ->orderByRaw('COALESCE(`order`, 999)')
                ->get();

            $out = [];
            foreach ($rows as $r) {
                $out[] = [
                    'code'  => strtoupper((string)$r->code),
                    'label' => $r->label ?: strtoupper((string)$r->code),
                    'color' => $r->color ?: '#111827',
                ];
            }
            // ensure some common fallbacks exist
            $has = collect($out)->pluck('code')->all();
            $ensure = [
                'H'  => ['Holiday', '#0EA5E9'],
                'LV' => ['Leave',   '#7C3AED'],
                'EL' => ['Leave',   '#7C3AED'],
            ];
            foreach ($ensure as $c => [$lbl,$clr]) {
                if (!in_array($c,$has, true)) $out[] = ['code'=>$c,'label'=>$lbl,'color'=>$clr];
            }
            return $out;
        }

        // Fallback when table missing
        return [
            ['code'=>'P',  'label'=>'Present',    'color'=>'#10B981'],
            ['code'=>'A',  'label'=>'Absent',     'color'=>'#EF4444'],
            ['code'=>'L',  'label'=>'Late',       'color'=>'#F59E0B'],
            ['code'=>'E',  'label'=>'Excused',    'color'=>'#3B82F6'],
            ['code'=>'HL', 'label'=>'Half-Leave', 'color'=>'#7C3AED'],
            ['code'=>'H',  'label'=>'Holiday',    'color'=>'#0EA5E9'],
            ['code'=>'LV', 'label'=>'Leave',      'color'=>'#7C3AED'],
            ['code'=>'EL', 'label'=>'Leave',      'color'=>'#7C3AED'],
        ];
    }

}
