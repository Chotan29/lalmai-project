<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

use App\Models\DepartmentHead;
use App\Models\Department;
use App\Models\Subject;
use App\Models\ClassRoutine;
use App\Models\Staff;

class AcademicDashboardController extends CollegeBaseController
{
    protected $base_route = 'academic.dashboard';
    protected $view_path  = 'academic.dashboard';
    protected $panel      = 'Academic Dashboard';

    public function index(Request $request)
    {
        try {
            $data = [];

            // Left-most level (Department Heads)
            $data['department_heads'] = DepartmentHead::query()
                ->where('status', 1)
                ->orderBy('department_head')
                ->pluck('department_head', 'id');

            // Initial filters (for deep-linking and pre-selection — like Fees Dashboard)
            $data['init_filters'] = [
                'department_head_id' => $request->query('department_head_id'),
                'department_id'      => $request->query('department_id'),
                'faculty_id'         => $request->query('faculty_id'),
                'semester_id'        => $request->query('semester_id'),
                'student_batch_id'   => $request->query('student_batch_id'),
                'subject_id'         => $request->query('subject_id'),
                'status'             => $request->query('status'), // optional
            ];

            // Default counters (UI will refresh via AJAX)
            $data['counters'] = [
                'routines'    => 0,
                'teachers'    => 0,
                'subjects'    => 0,
                'batches'     => 0,
                'semesters'   => 0,
                'faculties'   => 0,
                'departments' => 0,
                'active_pct'  => 0,
            ];

            return view(parent::loadDataToView($this->view_path.'.index'), $data);
        } catch (\Throwable $e) {
            Log::error('AcademicDashboard@index failed', ['error' => $e->getMessage()]);
            return back()->with('message_danger', 'Failed to load Academic Dashboard.');
        }
    }

    /**
     * AJAX: returns the data model for the dashboard widgets.
     */
    public function summaryChart(Request $request)
    {
        try {
            $filters = $request->only([
                'department_head_id',
                'department_id',
                'faculty_id',
                'semester_id',
                'student_batch_id',
                'subject_id',
                'status',
            ]);

            $q = ClassRoutine::query()->with([
                'department:id,department',
                'faculty:id,faculty',
                'semester:id,semester',
                'batch:id,title',
                'subject:id,title,code',
                'teacher:id,first_name,middle_name,last_name,reg_no,email',
            ]);

            if (!empty($filters['department_id']))    $q->where('department_id',    $filters['department_id']);
            if (!empty($filters['faculty_id']))       $q->where('faculty_id',       $filters['faculty_id']);
            if (!empty($filters['semester_id']))      $q->where('semester_id',      $filters['semester_id']);
            if (!empty($filters['student_batch_id'])) $q->where('student_batch_id', $filters['student_batch_id']);
            if (!empty($filters['subject_id']))       $q->where('subject_id',       $filters['subject_id']);
            if ($request->filled('status'))           $q->where('status', (int) $filters['status']);

            // Department Head scope -> departments under that head
            if (!empty($filters['department_head_id'])) {
                $deptIds = Department::whereHas('heads', function ($h) use ($filters) {
                    $h->where('department_heads.id', $filters['department_head_id']);
                })->pluck('id');

                if ($deptIds->isNotEmpty()) {
                    $q->whereIn('department_id', $deptIds);
                } else {
                    return response()->json([
                        'counters'        => $this->emptyCounters(),
                        'classesByDay'    => $this->blankDays(),
                        'topTeachers'     => [],
                        'topSubjects'     => [],
                        'nextSessions'    => [],
                        'roomUtilization' => ['labels'=>$this->hours24(),'data'=>array_fill(0,24,0)],
                        'attendance'      => ['present'=>0,'absent'=>0,'late'=>0],
                        'conflicts'       => [],
                        'modules'         => $this->modulesEmpty(),
                        'modulesBar'      => ['labels'=>[], 'active'=>[], 'inactive'=>[]],
                    ]);
                }
            }

            $base = (clone $q);

            // ---------- Counters ----------
            $totalRoutines  = (clone $base)->count();
            $activeRoutines = (clone $base)->where('status', 1)->count();
            $activePct      = $totalRoutines > 0 ? round(($activeRoutines / $totalRoutines) * 100) : 0;

            $counters = [
                'routines'    => $totalRoutines,
                'teachers'    => (clone $base)->whereNotNull('teacher_id')->distinct('teacher_id')->count('teacher_id'),
                'subjects'    => (clone $base)->whereNotNull('subject_id')->distinct('subject_id')->count('subject_id'),
                'batches'     => (clone $base)->whereNotNull('student_batch_id')->distinct('student_batch_id')->count('student_batch_id'),
                'semesters'   => (clone $base)->whereNotNull('semester_id')->distinct('semester_id')->count('semester_id'),
                'faculties'   => (clone $base)->whereNotNull('faculty_id')->distinct('faculty_id')->count('faculty_id'),
                'departments' => (clone $base)->whereNotNull('department_id')->distinct('department_id')->count('department_id'),
                'active_pct'  => $activePct,
            ];

            // ---------- Classes by day ----------
            $daysOrder   = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
            $rowsByDay   = (clone $base)->select('day_of_week', DB::raw('COUNT(*) as total'))
                            ->groupBy('day_of_week')->pluck('total','day_of_week')->toArray();
            $classesByDay = array_map(fn($d) => (int)($rowsByDay[$d] ?? 0), $daysOrder);

            // ---------- Top teachers ----------
            $topTeachersRows = (clone $base)->select('teacher_id', DB::raw('COUNT(*) as c'))
                ->whereNotNull('teacher_id')
                ->groupBy('teacher_id')->orderByDesc('c')->limit(10)->get();

            $teacherMap = Staff::whereIn('id', $topTeachersRows->pluck('teacher_id')->filter()->unique())
                ->get(['id','first_name','middle_name','last_name','reg_no','email'])
                ->keyBy('id');

            $topTeachers = $topTeachersRows->map(function ($r) use ($teacherMap) {
                $t = $teacherMap->get($r->teacher_id);
                $name = $t ? trim(implode(' ', array_filter([$t->first_name ?? null, $t->middle_name ?? null, $t->last_name ?? null])))
                           : null;
                $name = $name ?: ($t->reg_no ?? ($t->email ?? 'N/A'));
                return ['label' => $name, 'value' => (int)$r->c];
            })->values();

            // ---------- Top subjects ----------
            $topSubjectsRows = (clone $base)->select('subject_id', DB::raw('COUNT(*) as c'))
                ->whereNotNull('subject_id')
                ->groupBy('subject_id')->orderByDesc('c')->limit(10)->get();

            $subjectMap = Subject::whereIn('id', $topSubjectsRows->pluck('subject_id')->filter()->unique())
                ->get(['id','title','code'])
                ->keyBy('id');

            $topSubjects = $topSubjectsRows->map(function ($r) use ($subjectMap) {
                $s = $subjectMap->get($r->subject_id);
                $label = $s ? (($s->code ? $s->code.' — ' : '').($s->title ?? '')) : 'N/A';
                return ['label' => $label, 'value' => (int)$r->c];
            })->values();

            // ---------- Today snapshot ----------
            $tz        = config('app.timezone', 'Asia/Kathmandu');
            $now       = Carbon::now($tz);
            $todayName = $now->format('l');
            $todayYmd  = $now->toDateString();
            $nowHHMM   = $now->format('H:i');

            $nextSessions = (clone $base)->where('status', 1)->where('day_of_week', $todayName)
                ->where('start_time', '>=', $nowHHMM)->orderBy('start_time')->limit(8)->get()
                ->map(function ($r) {
                    $t = $r->teacher;
                    $teacherName = trim(implode(' ', array_filter([$t->first_name ?? null, $t->middle_name ?? null, $t->last_name ?? null]))) ?: '—';
                    return [
                        'start_time'     => $r->start_time,
                        'end_time'       => $r->end_time,
                        'room_number'    => $r->room_number,
                        'period'         => $r->period,
                        'subject_code'   => optional($r->subject)->code,
                        'subject_title'  => optional($r->subject)->title,
                        'teacher_name'   => $teacherName,
                        'teacher_reg_no' => $t->reg_no ?? ($t->email ?? '—'),
                        'faculty'        => optional($r->faculty)->faculty,
                        'semester'       => optional($r->semester)->semester,
                        'batch_title'    => optional($r->batch)->title,
                        'department'     => optional($r->department)->department,
                    ];
                })->values();

            // Hour buckets for utilization
            $hours = $this->hours24();
            $hourCounts = (clone $base)->where('status', 1)->where('day_of_week', $todayName)
                ->select(DB::raw("LEFT(start_time,2) as h"), DB::raw("COUNT(*) as c"))
                ->groupBy('h')->pluck('c','h')->toArray();

            $roomUtilization = [
                'labels' => $hours,
                'data'   => array_map(fn($h) => (int)($hourCounts[$h] ?? 0), $hours),
            ];

            // Attendance snapshot (optional table)
            $attendance = ['present'=>0,'absent'=>0,'late'=>0];
            if (Schema::hasTable('attendance_days')) {
                $rows = DB::table('attendance_days')
                    ->select('status', DB::raw('COUNT(*) as c'))
                    ->whereDate('day', $todayYmd)
                    ->groupBy('status')
                    ->pluck('c','status')->all();

                $attendance = [
                    'present' => (int)($rows['present'] ?? ($rows['PRESENT'] ?? 0)),
                    'absent'  => (int)($rows['absent']  ?? ($rows['ABSENT']  ?? 0)),
                    'late'    => (int)($rows['late']    ?? ($rows['LATE']    ?? 0)),
                ];
            }

            // ---------- Conflict monitor (today, same cohort overlap) ----------
            $conflicts = [];
            try {
                $pairs = DB::table('class_routines as a')
                    ->join('class_routines as b', function ($j) use ($todayName) {
                        $j->on('a.day_of_week', '=', 'b.day_of_week')
                          ->on('a.faculty_id',   '=', 'b.faculty_id')
                          ->on('a.semester_id',  '=', 'b.semester_id')
                          ->on('a.student_batch_id', '=', 'b.student_batch_id')
                          ->where('a.day_of_week', $todayName)
                          ->where('b.day_of_week', $todayName)
                          ->whereRaw('a.id < b.id')
                          ->whereRaw('a.start_time < b.end_time AND b.start_time < a.end_time');
                    })
                    ->select(
                        DB::raw('a.id as a_id'),
                        DB::raw('b.id as b_id'),
                        DB::raw('a.faculty_id as fac'),
                        DB::raw('a.semester_id as sem'),
                        DB::raw('a.student_batch_id as bat'),
                        DB::raw('a.start_time as a_start'),
                        DB::raw('a.end_time as a_end'),
                        DB::raw('b.start_time as b_start'),
                        DB::raw('b.end_time as b_end')
                    )
                    ->limit(50)
                    ->get();

                foreach ($pairs as $p) {
                    $conflicts[] = [
                        'a_id'   => $p->a_id,
                        'b_id'   => $p->b_id,
                        'day'    => $todayName,
                        'cohort' => sprintf('F:%s / S:%s / B:%s', $p->fac, $p->sem, $p->bat),
                        'a_time' => $p->a_start.'-'.$p->a_end,
                        'b_time' => $p->b_start.'-'.$p->b_end,
                    ];
                }
            } catch (\Throwable $e) {
                $conflicts = [];
            }

            // ---------- Modules (masters & dynamic) ----------
            $modules = $this->modulesSnapshot();

            $labels   = [];
            $active   = [];
            $inactive = [];
            foreach ($modules as $m) {
                $labels[]   = $m['label'];
                $active[]   = (int) $m['active'];
                $inactive[] = (int) $m['inactive'];
            }

            return response()->json([
                'counters'        => $counters,
                'classesByDay'    => $classesByDay,
                'topTeachers'     => $topTeachers,
                'topSubjects'     => $topSubjects,
                'nextSessions'    => $nextSessions,
                'roomUtilization' => $roomUtilization,
                'attendance'      => $attendance,
                'conflicts'       => $conflicts,
                'modules'         => $modules,
                'modulesBar'      => [
                    'labels'   => $labels,
                    'active'   => $active,
                    'inactive' => $inactive,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('AcademicDashboard@summaryChart failed', ['error' => $e->getMessage(), 'payload' => $request->all()]);
            return response()->json([
                'counters'        => $this->emptyCounters(),
                'classesByDay'    => $this->blankDays(),
                'topTeachers'     => [],
                'topSubjects'     => [],
                'nextSessions'    => [],
                'roomUtilization' => ['labels'=>$this->hours24(),'data'=>array_fill(0,24,0)],
                'attendance'      => ['present'=>0,'absent'=>0,'late'=>0],
                'conflicts'       => [],
                'modules'         => $this->modulesEmpty(),
                'modulesBar'      => ['labels'=>[], 'active'=>[], 'inactive'=>[]],
                'error'           => 'Failed to build summary.',
            ], 500);
        }
    }

    private function emptyCounters(): array
    {
        return [
            'routines'    => 0,
            'teachers'    => 0,
            'subjects'    => 0,
            'batches'     => 0,
            'semesters'   => 0,
            'faculties'   => 0,
            'departments' => 0,
            'active_pct'  => 0,
        ];
    }

    private function blankDays(): array { return [0,0,0,0,0,0,0]; }
    private function hours24(): array   { return array_map(fn($i)=>str_pad($i,2,'0',STR_PAD_LEFT), range(0,23)); }
    private function modulesEmpty(): array { return []; }

    private function countByTables(array $candidates): array
    {
        foreach ($candidates as $tbl) {
            if (Schema::hasTable($tbl)) {
                $total   = DB::table($tbl)->count();
                $active  = DB::table($tbl)->where('status', 1)->count();
                $inactive = max(0, $total - $active);
                return compact('total','active','inactive','tbl');
            }
        }
        return ['total'=>0,'active'=>0,'inactive'=>0,'tbl'=>null];
    }

    private function modulesSnapshot(): array
    {
        $map = [
            ['label'=>'Departments',        'tables'=>['departments']],
            ['label'=>'Faculties',          'tables'=>['faculties']],
            ['label'=>'Semesters',          'tables'=>['semesters']],
            ['label'=>'Student Batches',    'tables'=>['student_batches','batches']],
            ['label'=>'Subjects',           'tables'=>['subjects']],
            ['label'=>'Grading',            'tables'=>['gradings','grading_scales','grading']],
            ['label'=>'Student Status',     'tables'=>['student_statuses','student_status']],
            ['label'=>'Attendance Status',  'tables'=>['attendance_statuses','attendance_status']],
            ['label'=>'Book Status',        'tables'=>['book_statuses','book_status']],
            ['label'=>'Bed Status',         'tables'=>['bed_statuses','bed_status']],
            ['label'=>'Dynamic Degree',           'tables'=>['dynamic_degrees','degrees']],
            ['label'=>'Dynamic Scholarship',      'tables'=>['dynamic_scholarships','scholarships']],
            ['label'=>'Dynamic Placement',        'tables'=>['dynamic_placements','placements']],
            ['label'=>'Dynamic Annexure',         'tables'=>['dynamic_annexures','annexures']],
            ['label'=>'Dynamic Academic Level',   'tables'=>['dynamic_academic_info_levels','academic_info_levels']],
            ['label'=>'Dynamic State',            'tables'=>['dynamic_states','states']],
            ['label'=>'Dynamic Application Type', 'tables'=>['dynamic_application_types','application_types']],
        ];

        $out = [];
        foreach ($map as $m) {
            $counts = $this->countByTables($m['tables']);
            $out[] = [
                'label'    => $m['label'],
                'table'    => $counts['tbl'],
                'total'    => $counts['total'],
                'active'   => $counts['active'],
                'inactive' => $counts['inactive'],
            ];
        }
        return $out;
    }
}
