<?php
/**
 * Exam Mark Entry Progress Dashboard
 * Shows subject-wise mark entry completion, pending %, overdue alerts,
 * publish status, department-wise and teacher-wise summaries.
 */

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\CollegeBaseController;
use App\Models\Exam;
use App\Models\ExamMarkLedger;
use App\Models\ExamSchedule;
use App\Models\Faculty;
use App\Models\Month;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamDashboardController extends CollegeBaseController
{
    protected $base_route = 'exam.dashboard';
    protected $view_path = 'examination.dashboard';
    protected $panel = 'Exam Dashboard';

    public function index(Request $request)
    {
        $data = [];

        /* Filter dropdown data */
        $data['years'] = Year::select('id', 'title')->Active()->orderBy('title', 'desc')->get();
        $data['months'] = Month::select('id', 'title')->Active()->get();
        $data['exams'] = Exam::select('id', 'title')->Active()->get();
        $data['faculties'] = Faculty::select('id', 'faculty')->Active()->get();
        $data['semesters'] = Semester::select('id', 'semester')->Active()->get();

        /* Selected filters (default: active year) */
        $activeYear = Year::where('active_status', 1)->first();
        $filter = [
            'years_id' => $request->get('years_id', $activeYear ? $activeYear->id : null),
            'months_id' => $request->get('months_id'),
            'exams_id' => $request->get('exams_id'),
            'faculty_id' => $request->get('faculty_id'),
            'semesters_id' => $request->get('semesters_id'),
        ];
        $data['filter'] = $filter;

        /* Scheduled subjects with reference titles */
        $schedules = ExamSchedule::select(
                'exam_schedules.id', 'exam_schedules.date', 'exam_schedules.publish_status',
                'exam_schedules.faculty_id', 'exam_schedules.semesters_id', 'exam_schedules.subjects_id',
                'exam_schedules.years_id', 'exam_schedules.months_id', 'exam_schedules.exams_id',
                'exam_schedules.full_mark_theory', 'exam_schedules.full_mark_practical',
                'sub.full_mark_theory as sub_full_theory', 'sub.full_mark_practical as sub_full_practical',
                'sub.mcq_number_theory as sub_mcq_full',
                'sub.title as subject_title', 'sub.code as subject_code',
                'f.faculty as faculty_title', 'sem.semester as semester_title',
                'e.title as exam_title', 'm.title as month_title', 'y.title as year_title')
            ->join('subjects as sub', 'sub.id', '=', 'exam_schedules.subjects_id')
            ->join('faculties as f', 'f.id', '=', 'exam_schedules.faculty_id')
            ->join('semesters as sem', 'sem.id', '=', 'exam_schedules.semesters_id')
            ->join('exams as e', 'e.id', '=', 'exam_schedules.exams_id')
            ->join('months as m', 'm.id', '=', 'exam_schedules.months_id')
            ->join('years as y', 'y.id', '=', 'exam_schedules.years_id')
            ->where('exam_schedules.status', 1);

        foreach (['years_id', 'months_id', 'exams_id', 'faculty_id', 'semesters_id'] as $key) {
            if (!empty($filter[$key])) {
                $schedules->where('exam_schedules.' . $key, $filter[$key]);
            }
        }

        $schedules = $schedules->orderBy('f.faculty')->orderBy('sem.semester')->orderBy('sub.title')->get();

        $scheduleIds = $schedules->pluck('id')->all();

        /* Entry counts + absent counts + last entry per schedule (one query) */
        $ledgerStats = [];
        if (count($scheduleIds)) {
            $ledgerStats = ExamMarkLedger::select('exam_schedule_id',
                    DB::raw('COUNT(*) as entered'),
                    DB::raw('SUM(absent_theory) as absent_theory'),
                    DB::raw('SUM(absent_practical) as absent_practical'),
                    DB::raw('SUM(CASE WHEN obtain_mark_theory > 0 OR absent_theory = 1 THEN 1 ELSE 0 END) as theory_entered'),
                    DB::raw('SUM(CASE WHEN obtain_mark_mcq > 0 THEN 1 ELSE 0 END) as mcq_entered'),
                    DB::raw('SUM(CASE WHEN obtain_mark_practical > 0 OR absent_practical = 1 THEN 1 ELSE 0 END) as practical_entered'),
                    DB::raw('MAX(updated_at) as last_entry_at'),
                    DB::raw('MAX(created_by) as any_entry_by'))
                ->whereIn('exam_schedule_id', $scheduleIds)
                ->groupBy('exam_schedule_id')
                ->get()->keyBy('exam_schedule_id');
        }

        /* Last entry user per schedule */
        $lastEntryUsers = [];
        if (count($scheduleIds)) {
            $rows = DB::table('exam_mark_ledgers as l')
                ->select('l.exam_schedule_id', 'u.name', 'l.updated_at')
                ->leftJoin('users as u', 'u.id', '=', DB::raw('COALESCE(l.last_updated_by, l.created_by)'))
                ->whereIn('l.exam_schedule_id', $scheduleIds)
                ->orderBy('l.updated_at', 'desc')
                ->get();
            foreach ($rows as $r) {
                if (!isset($lastEntryUsers[$r->exam_schedule_id])) {
                    $lastEntryUsers[$r->exam_schedule_id] = $r->name;
                }
            }
        }

        /* Expected student count per faculty+semester (one grouped query) */
        $pairs = $schedules->map(function ($s) {
            return $s->faculty_id . '-' . $s->semesters_id;
        })->unique();

        $expectedCounts = [];
        if ($pairs->count()) {
            $counts = Student::select('faculty', 'semester', DB::raw('COUNT(*) as total'))
                ->whereIn(DB::raw("CONCAT(faculty,'-',semester)"), $pairs->all())
                ->Active()
                ->groupBy('faculty', 'semester')
                ->get();
            foreach ($counts as $c) {
                $expectedCounts[$c->faculty . '-' . $c->semester] = (int) $c->total;
            }
        }

        /* Build subject rows + aggregates */
        $today = Carbon::today();
        $summary = ['total' => 0, 'complete' => 0, 'partial' => 0, 'pending' => 0,
                    'published' => 0, 'unpublished' => 0,
                    'expected_entries' => 0, 'done_entries' => 0];
        $rows = [];
        $overdue = [];
        $deptSummary = [];

        foreach ($schedules as $s) {
            $stat = isset($ledgerStats[$s->id]) ? $ledgerStats[$s->id] : null;
            $entered = $stat ? (int) $stat->entered : 0;
            $expected = isset($expectedCounts[$s->faculty_id . '-' . $s->semesters_id])
                ? $expectedCounts[$s->faculty_id . '-' . $s->semesters_id] : 0;

            /* Applicable components: Theory / MCQ / Practical
               (full mark from schedule, falls back to subject master — same logic as mark entry page) */
            $theoryFull = $s->full_mark_theory > 0 ? $s->full_mark_theory : ($s->sub_full_theory ?: 0);
            $practicalFull = $s->full_mark_practical > 0 ? $s->full_mark_practical : ($s->sub_full_practical ?: 0);
            $mcqFull = $s->sub_mcq_full ?: 0;

            $components = [];
            if ($theoryFull > 0) {
                $components[] = ['label' => 'Theory', 'short' => 'T',
                                 'entered' => $stat ? (int) $stat->theory_entered : 0];
            }
            if ($mcqFull > 0) {
                $components[] = ['label' => 'MCQ', 'short' => 'M',
                                 'entered' => $stat ? (int) $stat->mcq_entered : 0];
            }
            if ($practicalFull > 0) {
                $components[] = ['label' => 'Practical', 'short' => 'P',
                                 'entered' => $stat ? (int) $stat->practical_entered : 0];
            }
            /* Fallback: nothing configured — treat theory as the only component */
            if (!count($components)) {
                $components[] = ['label' => 'Theory', 'short' => 'T',
                                 'entered' => $stat ? (int) $stat->theory_entered : 0];
            }

            /* Progress in entry-units: expected x number of applicable components */
            $expectedUnits = $expected * count($components);
            $doneUnits = 0;
            foreach ($components as $k => $c) {
                $components[$k]['expected'] = $expected;
                $components[$k]['done'] = $expected > 0 && $c['entered'] >= $expected;
                $doneUnits += min($c['entered'], $expected > 0 ? $expected : $c['entered']);
            }

            $percent = $expectedUnits > 0 ? round(min(100, ($doneUnits / $expectedUnits) * 100), 1)
                     : ($doneUnits > 0 ? 100 : 0);

            if ($doneUnits <= 0) {
                $status = 'pending';
            } elseif ($expectedUnits > 0 && $doneUnits < $expectedUnits) {
                $status = 'partial';
            } else {
                $status = 'complete';
            }

            $row = [
                'schedule' => $s,
                'expected' => $expected,
                'entered' => $entered,
                'components' => $components,
                'remaining' => max(0, $expectedUnits - $doneUnits),
                'absent' => $stat ? ((int) $stat->absent_theory) : 0,
                'percent' => $percent,
                'status' => $status,
                'last_entry_at' => $stat ? $stat->last_entry_at : null,
                'last_entry_by' => isset($lastEntryUsers[$s->id]) ? $lastEntryUsers[$s->id] : null,
                'is_overdue' => ($status != 'complete') && $s->date && Carbon::parse($s->date)->lt($today),
            ];
            $rows[] = $row;

            /* Aggregates */
            $summary['total']++;
            $summary[$status]++;
            $summary[$s->publish_status == 1 ? 'published' : 'unpublished']++;
            $summary['expected_entries'] += $expectedUnits;
            $summary['done_entries'] += $doneUnits;

            if ($row['is_overdue'] && $status == 'pending') {
                $overdue[] = $row;
            }

            /* Department-wise */
            $dKey = $s->faculty_id;
            if (!isset($deptSummary[$dKey])) {
                $deptSummary[$dKey] = ['title' => $s->faculty_title, 'total' => 0, 'complete' => 0,
                                       'expected' => 0, 'entered' => 0];
            }
            $deptSummary[$dKey]['total']++;
            if ($status == 'complete') $deptSummary[$dKey]['complete']++;
            $deptSummary[$dKey]['expected'] += $expectedUnits;
            $deptSummary[$dKey]['entered'] += $doneUnits;
        }

        $summary['overall_percent'] = $summary['expected_entries'] > 0
            ? round(($summary['done_entries'] / $summary['expected_entries']) * 100, 1) : 0;

        /* Teacher-wise entry summary (within filtered schedules) */
        $teacherSummary = [];
        if (count($scheduleIds)) {
            $teacherSummary = DB::table('exam_mark_ledgers as l')
                ->select('u.name', DB::raw('COUNT(DISTINCT l.exam_schedule_id) as subjects'),
                        DB::raw('COUNT(*) as entries'), DB::raw('MAX(l.updated_at) as last_entry'))
                ->leftJoin('users as u', 'u.id', '=', 'l.created_by')
                ->whereIn('l.exam_schedule_id', $scheduleIds)
                ->groupBy('u.name')
                ->orderBy('entries', 'desc')
                ->get();
        }

        $data['rows'] = $rows;
        $data['summary'] = $summary;
        $data['overdue'] = $overdue;
        $data['deptSummary'] = $deptSummary;
        $data['teacherSummary'] = $teacherSummary;

        return view(parent::loadDataToView($this->view_path . '.index'), compact('data'));
    }
}
