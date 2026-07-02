<?php

namespace App\Http\Controllers\Attendance\Dashboard;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Models\Student;
use App\Models\Attendance;

class StudentAttendanceDashboardController extends CollegeBaseController
{
    protected $base_route = 'attendance.dashboard.students.index';
    protected $view_path  = 'attendance.dashboard.students';
    protected $panel      = 'Student Attendance Dashboard';

    public function index(Request $request)
    {
        try {
            $today = Carbon::today();
            $from  = $request->query('from_date') ?: $today->copy()->subDays(29)->toDateString();
            $to    = $request->query('to_date')   ?: $today->toDateString();

            $data = [
                'from_date' => $from,
                'to_date'   => $to,
                'panel'     => $this->panel,
                'students_index_route' => route('attendance.dashboard.students.index'),
                'staff_index_route'    => route('attendance.dashboard.staff.index'),
            ];

            return view(parent::loadDataToView($this->view_path), $data);

        } catch (\Throwable $e) {
            Log::error('StudentAttendance@index failed', ['err' => $e->getMessage()]);
            return back()->with('message_danger', 'Failed to load Student Attendance Dashboard.');
        }
    }

    public function summary(Request $request)
    {
        try {
            [$from, $to] = $this->sanitizeDates(
                $request->query('from_date'),
                $request->query('to_date')
            );

            $statusCounts  = $this->getStatusCounts($from, $to);
            $trendData     = $this->getTrendData($from, $to);
            $facultyData   = $this->getFacultyData($from, $to);
            $semesterData  = $this->getSemesterData($from, $to);
            $batchData     = $this->getBatchData($from, $to);
            $departmentData= $this->getDepartmentData($from, $to);
            $genderData    = $this->getGenderData($from, $to);
            $ageData       = $this->getAgeGroupData($from, $to);

            return response()->json([
                'kpi' => [
                    'present' => $statusCounts['Present'] ?? 0,
                    'absent'  => $statusCounts['Absent'] ?? 0,
                    'late'    => $statusCounts['Late'] ?? 0,
                    'leave'   => $statusCounts['Leave'] ?? 0,
                    'holiday' => $statusCounts['Holiday'] ?? 0,
                    'total'   => array_sum($statusCounts),
                ],
                'statusPie' => [
                    'labels' => array_keys($statusCounts),
                    'data'   => array_values($statusCounts),
                ],
                'byDay' => [
                    'labels' => $trendData['labels'],
                    'data'   => $trendData['data'],
                ],
                'facultyWise' => [
                    'labels' => $facultyData['labels'],
                    'data'   => $facultyData['data'],
                ],
                'semesterWise' => [
                    'labels' => $semesterData['labels'],
                    'data'   => $semesterData['data'],
                ],
                'batchWise' => [
                    'labels' => $batchData['labels'],
                    'data'   => $batchData['data'],
                ],
                'departmentWise' => [
                    'labels' => $departmentData['labels'],
                    'data'   => $departmentData['data'],
                ],
                'genderPie' => $genderData,
                'ageWise' => [
                    'labels' => $ageData['labels'],
                    'data'   => $ageData['data'],
                ],
            ], 200);

        } catch (\Throwable $e) {
            Log::error('StudentAttendance@summary failed', ['err'=>$e->getMessage(), 'line'=>$e->getLine()]);
            return response()->json([
                'kpi' => ['present'=>0,'absent'=>0,'late'=>0,'leave'=>0,'holiday'=>0,'total'=>0],
                'statusPie'   => ['labels'=>[], 'data'=>[]],
                'byDay'       => ['labels'=>[], 'data'=>[]],
                'facultyWise' => ['labels'=>[], 'data'=>[]],
                'semesterWise'=> ['labels'=>[], 'data'=>[]],
                'batchWise'   => ['labels'=>[], 'data'=>[]],
                'departmentWise'=> ['labels'=>[], 'data'=>[]],
                'genderPie'   => ['labels'=>[], 'data'=>[]],
                'ageWise'     => ['labels'=>[], 'data'=>[]],
                'error' => 'Failed to load Student Attendance Dashboard.'
            ], 200);
        }
    }

    /* =========================
       Helper Methods
    ==========================*/

    private function sanitizeDates($from, $to)
    {
        $today = Carbon::today();
        $f = $from ? Carbon::parse($from) : $today->copy()->subDays(29);
        $t = $to   ? Carbon::parse($to)   : $today;
        if ($f->gt($t)) { $tmp=$f; $f=$t; $t=$tmp; }
        return [$f->toDateString(), $t->toDateString()];
    }

    private function studentMorphs(): array
    {
        return [Student::class, 'App\\Student', 'Student'];
    }

    private function getStatusCounts(string $from, string $to): array
    {
        $row = DB::table('attendances')
            ->leftJoin('attendance_statuses','attendances.attendance_status_id','=','attendance_statuses.id')
            ->whereBetween('attendances.date', [$from, $to])
            ->whereIn('attendances.attendable_type', $this->studentMorphs())
            ->selectRaw("
                SUM(CASE WHEN UPPER(attendance_statuses.code) IN ('P','PRESENT','1','Y','YES','TRUE') THEN 1 ELSE 0 END) AS present,
                SUM(CASE WHEN UPPER(attendance_statuses.code) IN ('A','ABSENT','0','N','NO','FALSE') THEN 1 ELSE 0 END) AS absent,
                SUM(CASE WHEN UPPER(attendance_statuses.code) LIKE 'L%' THEN 1 ELSE 0 END) AS late,
                SUM(CASE WHEN UPPER(attendance_statuses.code) LIKE 'H%' THEN 1 ELSE 0 END) AS holiday,
                SUM(CASE WHEN UPPER(attendance_statuses.code) IN ('LV','EL','CL','SL','E','HL') OR UPPER(attendance_statuses.code) LIKE '%LEAVE%' THEN 1 ELSE 0 END) AS leave_count,
                COUNT(*) AS total_rows
            ")
            ->first();

        $present = (int)($row->present ?? 0);
        $absent  = (int)($row->absent ?? 0);
        $late    = (int)($row->late ?? 0);
        $holiday = (int)($row->holiday ?? 0);
        $leave   = (int)($row->leave_count ?? 0);
        $total   = (int)($row->total_rows ?? 0);

        $other   = max(0, $total - ($present + $absent + $late + $holiday + $leave));

        return [
            'Present' => $present,
            'Absent'  => $absent,
            'Late'    => $late,
            'Leave'   => $leave,
            'Holiday' => $holiday,
            'Other'   => $other,
        ];
    }

    private function getTrendData($from, $to)
    {
        $dates = [];
        $current = Carbon::parse($from);
        $end = Carbon::parse($to);
        while ($current <= $end) {
            $dates[$current->toDateString()] = 0;
            $current->addDay();
        }

        $presentByDay = DB::table('attendances')
            ->leftJoin('attendance_statuses','attendances.attendance_status_id','=','attendance_statuses.id')
            ->whereBetween('attendances.date', [$from, $to])
            ->whereIn('attendances.attendable_type', $this->studentMorphs())
            ->where(function($q){
                $q->whereIn('attendance_statuses.code', ['P','Present','PRESENT','1','Y','YES','TRUE']);
            })
            ->selectRaw('DATE(attendances.date) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd')
            ->toArray();

        foreach ($dates as $d => $c) {
            if (isset($presentByDay[$d])) {
                $dates[$d] = (int)$presentByDay[$d];
            }
        }

        return [
            'labels' => array_keys($dates),
            'data'   => array_values($dates),
        ];
    }

    private function getFacultyData($from, $to)
    {
        $rows = DB::table('attendances')
            ->join('students', function($join){
                $join->on('attendances.attendable_id','=','students.id')
                     ->whereIn('attendances.attendable_type', $this->studentMorphs());
            })
            ->join('attendance_statuses','attendances.attendance_status_id','=','attendance_statuses.id')
            ->leftJoin('faculties','students.faculty','=','faculties.id')
            ->whereBetween('attendances.date', [$from, $to])
            ->whereIn('attendance_statuses.code', ['P','Present','PRESENT','1','Y','YES','TRUE'])
            ->select('faculties.faculty as name', DB::raw('COUNT(*) as count'))
            ->groupBy('faculties.id','faculties.faculty')
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $rows->pluck('name')->toArray(),
            'data'   => $rows->pluck('count')->map(fn($v)=>(int)$v)->toArray(),
        ];
    }

    private function getSemesterData($from, $to)
    {
        $rows = DB::table('attendances')
            ->join('students', function($join){
                $join->on('attendances.attendable_id','=','students.id')
                     ->whereIn('attendances.attendable_type', $this->studentMorphs());
            })
            ->join('attendance_statuses','attendances.attendance_status_id','=','attendance_statuses.id')
            ->leftJoin('semesters','students.semester','=','semesters.id')
            ->whereBetween('attendances.date', [$from, $to])
            ->whereIn('attendance_statuses.code', ['P','Present','PRESENT','1','Y','YES','TRUE'])
            ->select('semesters.semester as name', DB::raw('COUNT(*) as count'))
            ->groupBy('semesters.id','semesters.semester')
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $rows->pluck('name')->toArray(),
            'data'   => $rows->pluck('count')->map(fn($v)=>(int)$v)->toArray(),
        ];
    }

    private function getBatchData($from, $to)
    {
        $rows = DB::table('attendances')
            ->join('students', function($join){
                $join->on('attendances.attendable_id','=','students.id')
                     ->whereIn('attendances.attendable_type', $this->studentMorphs());
            })
            ->join('attendance_statuses','attendances.attendance_status_id','=','attendance_statuses.id')
            ->leftJoin('student_batches','students.batch','=','student_batches.id')
            ->whereBetween('attendances.date', [$from, $to])
            ->whereIn('attendance_statuses.code', ['P','Present','PRESENT','1','Y','YES','TRUE'])
            ->select('student_batches.title as name', DB::raw('COUNT(*) as count'))
            ->groupBy('student_batches.id','student_batches.title')
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $rows->pluck('name')->toArray(),
            'data'   => $rows->pluck('count')->map(fn($v)=>(int)$v)->toArray(),
        ];
    }

    private function getDepartmentData($from, $to)
    {
        $rows = DB::table('attendances')
            ->join('students', function($join){
                $join->on('attendances.attendable_id','=','students.id')
                     ->whereIn('attendances.attendable_type', $this->studentMorphs());
            })
            ->join('attendance_statuses','attendances.attendance_status_id','=','attendance_statuses.id')
            ->leftJoin('faculties','students.faculty','=','faculties.id')
            ->leftJoin('department_programs','faculties.id','=','department_programs.faculty_id')
            ->leftJoin('departments','department_programs.department_id','=','departments.id')
            ->whereBetween('attendances.date', [$from, $to])
            ->whereIn('attendance_statuses.code', ['P','Present','PRESENT','1','Y','YES','TRUE'])
            ->select('departments.department as name', DB::raw('COUNT(*) as count'))
            ->groupBy('departments.id','departments.department')
            ->orderByDesc('count')
            ->get();

        if ($rows->isEmpty()) {
            $rows = DB::table('attendances')
                ->join('students', function($join){
                    $join->on('attendances.attendable_id','=','students.id')
                         ->whereIn('attendances.attendable_type', $this->studentMorphs());
                })
                ->join('attendance_statuses','attendances.attendance_status_id','=','attendance_statuses.id')
                ->whereBetween('attendances.date', [$from, $to])
                ->whereIn('attendance_statuses.code', ['P','Present','PRESENT','1','Y','YES','TRUE'])
                ->select(DB::raw("'General' as name"), DB::raw('COUNT(*) as count'))
                ->groupBy('name')
                ->orderByDesc('count')
                ->get();
        }

        return [
            'labels' => $rows->pluck('name')->toArray(),
            'data'   => $rows->pluck('count')->map(fn($v)=>(int)$v)->toArray(),
        ];
    }

    private function getGenderData($from, $to)
    {
        $rows = DB::table('attendances')
            ->join('students', function($join){
                $join->on('attendances.attendable_id','=','students.id')
                     ->whereIn('attendances.attendable_type', $this->studentMorphs());
            })
            ->join('attendance_statuses','attendances.attendance_status_id','=','attendance_statuses.id')
            ->whereBetween('attendances.date', [$from, $to])
            ->whereIn('attendance_statuses.code', ['P','Present','PRESENT','1','Y','YES','TRUE'])
            ->select('students.gender', DB::raw('COUNT(*) as count'))
            ->groupBy('students.gender')
            ->get();

        $agg = ['Male'=>0,'Female'=>0,'Other'=>0];
        foreach ($rows as $r) {
            $g = strtoupper(trim($r->gender ?? ''));
            if ($g === 'M' || $g === 'MALE')        $agg['Male']   += (int)$r->count;
            elseif ($g === 'F' || $g === 'FEMALE')  $agg['Female'] += (int)$r->count;
            else                                    $agg['Other']  += (int)$r->count;
        }

        return ['labels'=>array_keys($agg), 'data'=>array_values($agg)];
    }

    private function getAgeGroupData($from, $to)
    {
        $rows = DB::table('attendances')
            ->join('students', function($join){
                $join->on('attendances.attendable_id','=','students.id')
                     ->whereIn('attendances.attendable_type', $this->studentMorphs());
            })
            ->join('attendance_statuses','attendances.attendance_status_id','=','attendance_statuses.id')
            ->whereBetween('attendances.date', [$from, $to])
            ->whereIn('attendance_statuses.code', ['P','Present','PRESENT','1','Y','YES','TRUE'])
            ->select(DB::raw("
                CASE 
                    WHEN FLOOR(DATEDIFF(CURDATE(), students.date_of_birth)/365) <= 10 THEN '0-10'
                    WHEN FLOOR(DATEDIFF(CURDATE(), students.date_of_birth)/365) <= 15 THEN '11-15'
                    WHEN FLOOR(DATEDIFF(CURDATE(), students.date_of_birth)/365) <= 20 THEN '16-20'
                    ELSE '21+' 
                END as age_group
            "), DB::raw('COUNT(*) as count'))
            ->groupBy('age_group')
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $rows->pluck('age_group')->toArray(),
            'data'   => $rows->pluck('count')->map(fn($v)=>(int)$v)->toArray(),
        ];
    }

    private function normalizeStatus($v)
    {
        $t = strtoupper(trim((string)$v));
        if ($t === 'P' || str_contains($t,'PRESENT') || $t==='1' || $t==='Y' || $t==='YES' || $t==='TRUE') return 'Present';
        if ($t === 'A' || str_contains($t,'ABSENT')  || $t==='0' || $t==='N' || $t==='NO'  || $t==='FALSE') return 'Absent';
        if ($t === 'L' || str_contains($t,'LATE')) return 'Late';
        if ($t === 'H' || str_contains($t,'HOLIDAY')) return 'Holiday';
        if (str_contains($t,'LEAVE') || in_array($t, ['LV','EL','CL','SL','E','HL'])) return 'Leave';
        return 'Other';
    }
}
