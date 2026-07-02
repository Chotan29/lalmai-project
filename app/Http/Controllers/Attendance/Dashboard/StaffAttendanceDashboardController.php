<?php

namespace App\Http\Controllers\Attendance\Dashboard;

use App\Http\Controllers\CollegeBaseController;
use App\Models\Staff;
use App\Models\Attendance;
use App\Models\StaffDesignation;
use App\Models\AttendanceStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StaffAttendanceDashboardController extends CollegeBaseController
{
    protected $base_route = 'attendance.dashboard.staff.index';
    protected $view_path = 'attendance.dashboard.staff';
    protected $panel = 'Staff Attendance Dashboard';

    public function index(Request $request)
    {
        try {
            $today = Carbon::today();
            $from = $request->query('from_date') ?: $today->copy()->subDays(29)->toDateString();
            $to = $request->query('to_date') ?: $today->toDateString();

            $data = [
                'from_date' => $from,
                'to_date' => $to,
                'panel' => $this->panel,
                'students_index_route' => route('attendance.dashboard.students.index'),
                'staff_index_route' => route('attendance.dashboard.staff.index'),
            ];

            return view(parent::loadDataToView($this->view_path), $data);
        } catch (\Throwable $e) {
            Log::error('StaffAttendance@index failed', ['err' => $e->getMessage()]);
            return back()->with('message_danger', 'Failed to load Staff Attendance Dashboard.');
        }
    }

    public function summary(Request $request)
    {
        try {
            [$from, $to] = $this->sanitizeDates(
                $request->query('from_date'),
                $request->query('to_date')
            );

            $statusCounts = $this->getStatusCounts($from, $to);
            $trendData = $this->getTrendData($from, $to);
            $designationData = $this->getDesignationData($from, $to);
            $genderData = $this->getGenderData($from, $to);
            $ageData = $this->getAgeGroupData($from, $to);

            return response()->json([
                'kpi' => [
                    'present' => $statusCounts['Present'] ?? 0,
                    'absent' => $statusCounts['Absent'] ?? 0,
                    'late' => $statusCounts['Late'] ?? 0,
                    'leave' => $statusCounts['Leave'] ?? 0,
                    'holiday' => $statusCounts['Holiday'] ?? 0,
                    'total' => array_sum($statusCounts),
                ],
                'statusPie' => [
                    'labels' => array_keys($statusCounts),
                    'data' => array_values($statusCounts),
                ],
                'byDay' => [
                    'labels' => $trendData['labels'],
                    'data' => $trendData['data'],
                ],
                'designationWise' => [
                    'labels' => $designationData['labels'],
                    'data' => $designationData['data'],
                ],
                'genderPie' => $genderData,
                'ageWise' => [
                    'labels' => $ageData['labels'],
                    'data' => $ageData['data'],
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('StaffAttendance@summary failed', ['err' => $e->getMessage(), 'line' => $e->getLine()]);
            return response()->json([
                'kpi' => ['present' => 0, 'absent' => 0, 'late' => 0, 'leave' => 0, 'holiday' => 0, 'total' => 0],
                'statusPie' => ['labels' => [], 'data' => []],
                'byDay' => ['labels' => [], 'data' => []],
                'designationWise' => ['labels' => [], 'data' => []],
                'genderPie' => ['labels' => [], 'data' => []],
                'ageWise' => ['labels' => [], 'data' => []],
                'error' => 'Failed to load Staff Attendance Dashboard.'
            ], 200);
        }
    }

    private function sanitizeDates($from, $to)
    {
        $today = Carbon::today();
        $f = $from ? Carbon::parse($from) : $today->copy()->subDays(29);
        $t = $to ? Carbon::parse($to) : $today;
        if ($f->gt($t)) [$f, $t] = [$t, $f];
        return [$f->toDateString(), $t->toDateString()];
    }

    private function staffMorphs(): array
    {
        return [Staff::class, 'App\\Models\\Staff', 'Staff'];
    }

    private function getStatusCounts(string $from, string $to): array
    {
        $row = DB::table('attendances')
            ->leftJoin('attendance_statuses', 'attendances.attendance_status_id', '=', 'attendance_statuses.id')
            ->whereBetween('attendances.date', [$from, $to])
            ->whereIn('attendances.attendable_type', $this->staffMorphs())
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
        $absent = (int)($row->absent ?? 0);
        $late = (int)($row->late ?? 0);
        $holiday = (int)($row->holiday ?? 0);
        $leave = (int)($row->leave_count ?? 0);
        $total = (int)($row->total_rows ?? 0);
        $other = max(0, $total - ($present + $absent + $late + $holiday + $leave));

        return [
            'Present' => $present,
            'Absent' => $absent,
            'Late' => $late,
            'Leave' => $leave,
            'Holiday' => $holiday,
            'Other' => $other,
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
            ->leftJoin('attendance_statuses', 'attendances.attendance_status_id', '=', 'attendance_statuses.id')
            ->whereBetween('attendances.date', [$from, $to])
            ->whereIn('attendances.attendable_type', $this->staffMorphs())
            ->whereIn('attendance_statuses.code', ['P', 'Present', 'PRESENT', '1', 'Y', 'YES', 'TRUE'])
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
            'data' => array_values($dates),
        ];
    }

    private function getDesignationData($from, $to)
    {
        $rows = DB::table('attendances')
            ->join('staff', function ($join) {
                $join->on('attendances.attendable_id', '=', 'staff.id')
                     ->whereIn('attendances.attendable_type', $this->staffMorphs());
            })
            ->join('attendance_statuses', 'attendances.attendance_status_id', '=', 'attendance_statuses.id')
            ->leftJoin('staff_designations', 'staff.designation', '=', 'staff_designations.id')
            ->whereBetween('attendances.date', [$from, $to])
            ->whereIn('attendance_statuses.code', ['P', 'Present', 'PRESENT', '1', 'Y', 'YES', 'TRUE'])
            ->selectRaw('COALESCE(staff_designations.title, "Unknown") as name, COUNT(*) as count')
            ->groupBy('staff_designations.id', 'staff_designations.title')
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $rows->pluck('name')->toArray(),
            'data' => $rows->pluck('count')->map(fn($v) => (int)$v)->toArray(),
        ];
    }

    private function getGenderData($from, $to)
    {
        $rows = DB::table('attendances')
            ->join('staff', function ($join) {
                $join->on('attendances.attendable_id', '=', 'staff.id')
                     ->whereIn('attendances.attendable_type', $this->staffMorphs());
            })
            ->join('attendance_statuses', 'attendances.attendance_status_id', '=', 'attendance_statuses.id')
            ->whereBetween('attendances.date', [$from, $to])
            ->whereIn('attendance_statuses.code', ['P', 'Present', 'PRESENT', '1', 'Y', 'YES', 'TRUE'])
            ->select('staff.gender', DB::raw('COUNT(*) as count'))
            ->groupBy('staff.gender')
            ->get();

        $agg = ['Male' => 0, 'Female' => 0, 'Other' => 0];
        foreach ($rows as $r) {
            $g = strtoupper(trim($r->gender ?? ''));
            if ($g === 'M' || $g === 'MALE') $agg['Male'] += (int)$r->count;
            elseif ($g === 'F' || $g === 'FEMALE') $agg['Female'] += (int)$r->count;
            else $agg['Other'] += (int)$r->count;
        }

        return ['labels' => array_keys($agg), 'data' => array_values($agg)];
    }

    private function getAgeGroupData($from, $to)
    {
        $rows = DB::table('attendances')
            ->join('staff', function ($join) {
                $join->on('attendances.attendable_id', '=', 'staff.id')
                     ->whereIn('attendances.attendable_type', $this->staffMorphs());
            })
            ->join('attendance_statuses', 'attendances.attendance_status_id', '=', 'attendance_statuses.id')
            ->whereBetween('attendances.date', [$from, $to])
            ->whereIn('attendance_statuses.code', ['P', 'Present', 'PRESENT', '1', 'Y', 'YES', 'TRUE'])
            ->selectRaw("
                CASE 
                    WHEN FLOOR(DATEDIFF(CURDATE(), staff.date_of_birth)/365) <= 30 THEN '≤30'
                    WHEN FLOOR(DATEDIFF(CURDATE(), staff.date_of_birth)/365) <= 40 THEN '31-40'
                    WHEN FLOOR(DATEDIFF(CURDATE(), staff.date_of_birth)/365) <= 50 THEN '41-50'
                    ELSE '51+' 
                END as age_group, COUNT(*) as count")
            ->groupBy('age_group')
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $rows->pluck('age_group')->toArray(),
            'data' => $rows->pluck('count')->map(fn($v) => (int)$v)->toArray(),
        ];
    }
}