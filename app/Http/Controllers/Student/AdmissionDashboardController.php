<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\CollegeBaseController;
use App\Models\OnlinePayment;
use App\Models\OnlineRegistrationProgram;
use App\Models\OnlineRegistrationSetting;
use App\Models\Student;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

/**
 * Admission Dashboard
 *
 * One screen for everything admission related: how many applied, department-wise
 * intake and fees, what was collected, which applications are still unfinished or
 * waiting to be activated, and shortcuts to every admission action. It only reads
 * data - all changes still happen on the existing screens it links to.
 */
class AdmissionDashboardController extends CollegeBaseController
{
    protected $base_route = 'admission-dashboard';
    protected $view_path  = 'student.admission-dashboard';
    protected $panel      = 'Admission Dashboard';

    public function index(Request $request)
    {
        $data = [];

        /* Registration window / settings */
        $setting = OnlineRegistrationSetting::where('status', 'active')
            ->orWhere('status', 1)
            ->first() ?? OnlineRegistrationSetting::first();
        $data['setting'] = $setting;

        /* Which session (batch) to report on. Default = the batch with the most
           applications, so the dashboard opens on the running admission. */
        $data['batches'] = DB::table('student_batches')->orderBy('id', 'desc')
            ->pluck('title', 'id')->toArray();

        $selectedBatch = $request->get('batch');
        if (!$selectedBatch) {
            $selectedBatch = DB::table('students')
                ->select('batch', DB::raw('COUNT(*) as c'))
                ->whereNotNull('batch')
                ->groupBy('batch')->orderByDesc('c')->value('batch');
        }
        $data['selected_batch'] = $selectedBatch;

        $scope = function ($query) use ($selectedBatch) {
            if ($selectedBatch) {
                $query->where('students.batch', $selectedBatch);
            }
            return $query;
        };

        /* ---------------- headline numbers ---------------- */
        $total    = $scope(Student::query())->count();
        $active   = $scope(Student::query())->where('students.status', 1)->count();
        $inactive = $total - $active;
        $newStu   = $scope(Student::query())->where('students.student_type', 'new')->count();
        $oldStu   = $scope(Student::query())->where('students.student_type', 'old')->count();
        $today    = $scope(Student::query())->whereDate('students.created_at', now()->toDateString())->count();
        $week     = $scope(Student::query())->where('students.created_at', '>=', now()->subDays(7))->count();

        $data['stats'] = [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'new' => $newStu,
            'old' => $oldStu,
            'today' => $today,
            'week' => $week,
        ];

        /* ---------------- money ---------------- */
        $paymentQuery = OnlinePayment::query();
        if ($selectedBatch) {
            $paymentQuery->whereIn('students_id', function ($q) use ($selectedBatch) {
                $q->select('id')->from('students')->where('batch', $selectedBatch);
            });
        }
        $data['payment'] = [
            'count'      => (clone $paymentQuery)->count(),
            'total'      => (float) (clone $paymentQuery)->sum('amount'),
            'verified'   => (clone $paymentQuery)->where('status', 1)->count(),
            'unverified' => (clone $paymentQuery)->where('status', 0)->count(),
        ];

        /* Paid but never finished - the payloads the Payment Recovery screen works on. */
        $dir = storage_path('app/pending_payments');
        $pendingFiles = is_dir($dir) ? glob($dir . DIRECTORY_SEPARATOR . '*.json') : [];
        $data['pending_payments'] = count($pendingFiles);

        $data['recent_pending'] = [];
        usort($pendingFiles, function ($a, $b) { return filemtime($b) - filemtime($a); });
        foreach (array_slice($pendingFiles, 0, 5) as $file) {
            $payload = json_decode(@file_get_contents($file), true);
            if (!is_array($payload)) { continue; }
            $reg = $payload['registration_data'] ?? [];
            $data['recent_pending'][] = (object) [
                'ref'   => basename($file, '.json'),
                'name'  => trim(($reg['first_name'] ?? '') . ' ' . ($reg['last_name'] ?? '')) ?: 'Unknown',
                'amount' => $payload['amount'] ?? 0,
                'at'    => $payload['initiated_at'] ?? date('Y-m-d H:i', @filemtime($file)),
            ];
        }

        /* ---------------- department-wise ---------------- */
        $rows = DB::table('students as s')
            ->leftJoin('faculties as f', 'f.id', '=', 's.faculty')
            ->when($selectedBatch, function ($q) use ($selectedBatch) {
                return $q->where('s.batch', $selectedBatch);
            })
            ->select('s.faculty', 'f.faculty as faculty_name',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN s.status = 1 THEN 1 ELSE 0 END) as active'),
                DB::raw("SUM(CASE WHEN s.student_type = 'new' THEN 1 ELSE 0 END) as new_count"),
                DB::raw("SUM(CASE WHEN s.student_type = 'old' THEN 1 ELSE 0 END) as old_count"))
            ->groupBy('s.faculty', 'f.faculty')
            ->orderByDesc('total')
            ->get();

        /* Attach the configured fee of each department so the whole admission picture
           (intake + price) is visible in one table. */
        $programFees = OnlineRegistrationProgram::select('faculties_id', 'new_student_fee', 'old_student_fee', 'status')
            ->get()->groupBy('faculties_id');

        foreach ($rows as $row) {
            $program = $programFees->get($row->faculty);
            $first = $program ? $program->first() : null;
            $row->new_fee = $first ? $first->new_student_fee : null;
            $row->old_fee = $first ? $first->old_student_fee : null;
            $row->program_open = $first ? (int) $first->status === 1 : false;
            $row->expected = ((float) $row->new_count * (float) ($row->new_fee ?: 0))
                           + ((float) $row->old_count * (float) ($row->old_fee ?: 0));
        }
        $data['departments'] = $rows;

        /* Departments that accept registration but have no fee set - these silently
           block payment, so they are worth flagging at the top of the dashboard. */
        $data['fee_missing'] = OnlineRegistrationProgram::leftJoin('faculties as f', 'f.id', '=', 'online_registration_programs.faculties_id')
            ->where(function ($q) {
                $q->whereNull('online_registration_programs.new_student_fee')
                  ->orWhere('online_registration_programs.new_student_fee', '<=', 0);
            })
            ->select('f.faculty as faculty_name')
            ->pluck('faculty_name')->filter()->values();

        /* ---------------- lists ---------------- */
        $data['recent'] = DB::table('students as s')
            ->leftJoin('faculties as f', 'f.id', '=', 's.faculty')
            ->when($selectedBatch, function ($q) use ($selectedBatch) {
                return $q->where('s.batch', $selectedBatch);
            })
            ->select('s.id', 's.reg_no', 's.first_name', 's.last_name', 's.student_type',
                     's.status', 's.created_at', 's.email', 'f.faculty as faculty_name')
            ->orderByDesc('s.id')->limit(10)->get();

        $data['waiting_activation'] = DB::table('students as s')
            ->leftJoin('faculties as f', 'f.id', '=', 's.faculty')
            ->when($selectedBatch, function ($q) use ($selectedBatch) {
                return $q->where('s.batch', $selectedBatch);
            })
            ->where('s.status', 0)
            ->select('s.id', 's.reg_no', 's.first_name', 's.last_name', 's.created_at', 'f.faculty as faculty_name')
            ->orderByDesc('s.id')->limit(10)->get();

        /* Logins still locked for admitted students */
        $data['inactive_logins'] = User::where('role_id', 6)->where('status', 0)->count();

        /* Applications per day for the last 14 days (simple trend) */
        $data['trend'] = DB::table('students')
            ->when($selectedBatch, function ($q) use ($selectedBatch) {
                return $q->where('batch', $selectedBatch);
            })
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
            ->groupBy('d')->orderBy('d')->get();

        $data['url'] = URL::current();

        return view(parent::loadDataToView($this->view_path . '.index'), compact('data'));
    }
}
