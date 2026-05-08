<?php

namespace App\Http\Controllers\Account\Fees;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

use App\Models\FeeCollection;
use App\Models\FeeHead;
use App\Models\OnlinePayment;

class FeesDashboardController extends CollegeBaseController
{
    protected $base_route = 'fees.dashboard';
    protected $view_path  = 'account.fees.dashboard';
    protected $panel      = 'Fees Dashboard';

    public function index(Request $request)
    {
        try {
            $today = Carbon::today();
            $from  = $request->query('from_date') ?: $today->copy()->subDays(29)->toDateString();
            $to    = $request->query('to_date')   ?: $today->toDateString();

            $departmentHeads = \App\Models\DepartmentHead::query()
                ->where('status', 1)
                ->orderBy('department_head')
                ->pluck('department_head', 'id');

            $data = [
                'department_heads' => $departmentHeads,
                'from_date'        => $from,
                'to_date'          => $to,
                'currency'         => strtoupper(env('CURRENCY')),
                'init_filters'     => [
                    'department_head_id' => $request->query('department_head_id'),
                    'department_id'      => $request->query('department_id'),
                    'faculty_id'         => $request->query('faculty_id'),
                    'semester_id'        => $request->query('semester_id'),
                    'student_batch_id'   => $request->query('student_batch_id'),
                ],
            ];

            return view(parent::loadDataToView($this->view_path.'.index'), $data);

        } catch (\Throwable $e) {
            Log::error('FeesDashboard@index failed', ['err' => $e->getMessage()]);
            return back()->with('message_danger', 'Failed to load Fees Dashboard.');
        }
    }

    public function summary(Request $request)
    {
        try {
            $filters = $request->only([
                'department_head_id', 'department_id', 'faculty_id',
                'semester_id', 'student_batch_id', 'from_date', 'to_date'
            ]);

            [$from, $to] = $this->sanitizeDates($filters['from_date'] ?? null, $filters['to_date'] ?? null);

            [$fcDateCol, $fcAmountCol, $fcHeadIdCol, $fcStudentCol, $fcMethodCol, $fcReceiptCol, $fcMasterIdCol] = $this->feeCollectionColumns();
            [$opDateCol, $opAmountCol, $opStatusCol, $opVerifyCol, $opStudentCol]                                 = $this->onlinePaymentColumns();

            $fcDate     = $this->qcol('fee_collections', $fcDateCol);
            $fcAmount   = $this->qcol('fee_collections', $fcAmountCol);
            $fcHeadIdQ  = $fcHeadIdCol ? $this->qcol('fee_collections', $fcHeadIdCol) : null;
            $fcMethodQ  = $fcMethodCol ? $this->qcol('fee_collections', $fcMethodCol) : null;

            $opDate     = $this->qcol('online_payments', $opDateCol);
            $opAmount   = $this->qcol('online_payments', $opAmountCol);
            $opStatusQ  = $opStatusCol ? $this->qcol('online_payments', $opStatusCol) : null;
            $opVerifyQ  = $opVerifyCol ? $this->qcol('online_payments', $opVerifyCol) : null;

            // ---- Base queries (with hierarchy joins/filters) ----
            $fc = FeeCollection::query()->from('fee_collections');
            $this->applyHierarchy($fc, 'fee_collections', $filters, $fcStudentCol);
            $this->applyDateRange($fc, 'fee_collections', $fcDateCol, $from, $to);

            $op = OnlinePayment::query()->from('online_payments');
            $this->applyHierarchy($op, 'online_payments', $filters, $opStudentCol);
            $this->applyDateRange($op, 'online_payments', $opDateCol, $from, $to);

            // ---- KPIs ----
            $receipts  = (clone $fc)->count();
            $collected = (float) (clone $fc)->sum($fcAmount);
            $discount  = Schema::hasColumn('fee_collections', 'discount') ? (float)(clone $fc)->sum('fee_collections.discount') : 0.0;
            $fine      = Schema::hasColumn('fee_collections', 'fine')     ? (float)(clone $fc)->sum('fee_collections.fine')     : 0.0;
            $net       = max(0, $collected - $discount + $fine);

            $studentsPaid = 0;
            if ($fcStudentCol) {
                $studentsPaid = (clone $fc)
                    ->distinct('fee_collections.' . $fcStudentCol)
                    ->count('fee_collections.' . $fcStudentCol);
            }
            $avgReceipt = $receipts > 0 ? round(($collected ?: 0) / $receipts, 2) : 0.0;

            $onlinePaid    = $this->sumWhereFlexible($op, $opAmount, $opStatusQ, ['PAID','SUCCESS',1,'1','TRUE','OK']);
            $onlinePending = $this->sumWhereFlexible($op, $opAmount, $opStatusQ, ['PENDING','INIT',0,'0','UNVERIFIED','PROCESSING']);

            // ---- Charts ----
            // By day
            $byDayRows = (clone $fc)
                ->select(DB::raw("DATE($fcDate) as d"), DB::raw("SUM($fcAmount) as s"))
                ->groupBy(DB::raw("DATE($fcDate)"))
                ->orderBy(DB::raw("DATE($fcDate)"))
                ->get();

            $labelsByDay = $byDayRows->pluck('d')->values();
            $valsByDay   = $byDayRows->pluck('s')->map(fn($v)=> (float)$v)->values();

            $collectionsByDay = ['labels' => $labelsByDay, 'data' => $valsByDay];

            $running = 0.0; $cumul = [];
            foreach ($valsByDay as $v) { $running += (float)$v; $cumul[] = $running; }
            $cumulativeByDay = ['labels' => $labelsByDay, 'data' => $cumul];

            // Top heads (make labels = fee_head_title when possible)
            $headsAgg  = $this->buildTopHeadsAggregates($fc, $fcHeadIdCol, $fcHeadIdQ, $fcAmount, $fcMasterIdCol);
            $topHeads  = $headsAgg['chart'];
            $topTable  = $headsAgg['table'];
            $headShare = [
                'labels' => collect($topHeads)->pluck('label')->values(),
                'data'   => collect($topHeads)->pluck('value')->values(),
            ];

            // Payment methods + by day + mode split
            $paymentMethods = $this->buildPaymentMethods($fc, $fcMethodCol, $fcMethodQ, $fcAmount);
            $pmByDay        = $this->buildPaymentMethodsByDay($fc, $fcDate, $fcAmount, $fcMethodQ);
            $modeSplit      = $this->buildModeSplitByDay($fc, $fcDate, $fcAmount, $fcMethodQ);

            // Online timeline + status
            [$uniqCol, $uniqQ] = $this->onlineUniqueIdColumn();
            $opTimelineRows = (clone $op)
                ->select(DB::raw("DATE($opDate) as d"), DB::raw("COUNT(DISTINCT $uniqQ) as c"))
                ->groupBy(DB::raw("DATE($opDate)"))
                ->orderBy(DB::raw("DATE($opDate)"))
                ->get();

            $onlineTimeline = [
                'labels' => $opTimelineRows->pluck('d')->values(),
                'data'   => $opTimelineRows->pluck('c')->map(fn($v)=> (int)$v)->values(),
            ];
            $onlineStatus = $this->buildOnlineStatusPie($op, $opStatusCol, $opStatusQ, $opVerifyCol, $opVerifyQ);

            // Recent receipts (detailed)
            $rc = (clone $fc);
            $this->joinStudentsIfNeeded($rc, 'fee_collections', $fcStudentCol, $filters);
            $stReg = $this->studentsRegNoColumn();
            [$stName, $stFirst, $stMiddle, $stLast] = $this->studentsNameColumns();

            $selects = ['fee_collections.*'];
            if ($stReg)   $selects[] = "st.$stReg as st_reg_no";
            if ($stName)  $selects[] = "st.$stName as st_name";
            if ($stFirst) $selects[] = "st.$stFirst as st_first_name";
            if ($stMiddle)$selects[] = "st.$stMiddle as st_middle_name";
            if ($stLast)  $selects[] = "st.$stLast as st_last_name";

            $rcRows = $rc->select($selects)->orderBy($fcDate, 'desc')->limit(10)->get();
            $recent = $this->formatRecentReceiptsDetailed($rcRows, $fcDateCol, $fcAmountCol, $fcMethodCol);

            return response()->json([
                'counters' => [
                    'collected'       => (float)$collected,
                    'receipts'        => (int)$receipts,
                    'students_paid'   => (int)$studentsPaid,
                    'avg_receipt'     => (float)$avgReceipt,
                    'online_paid'     => (float)$onlinePaid,
                    'online_pending'  => (float)$onlinePending,
                    'discount'        => (float)$discount,
                    'fine'            => (float)$fine,
                    'net'             => (float)$net,
                ],
                'collectionsByDay' => $collectionsByDay,
                'cumulativeByDay'  => $cumulativeByDay,
                'topHeads'         => $topHeads,
                'topHeadsTable'    => $topTable,
                'headShare'        => $headShare,
                'paymentMethods'   => $paymentMethods,
                'methodsByDay'     => $pmByDay,
                'modeSplitByDay'   => $modeSplit,
                'onlineTimeline'   => $onlineTimeline,
                'onlineStatus'     => $onlineStatus,
                'recentReceipts'   => $recent,
            ]);
        } catch (\Throwable $e) {
            Log::error('FeesDashboard@summary failed', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
                'req'   => $request->all(),
            ]);

            return response()->json([
                'counters' => [
                    'collected' => 0, 'receipts' => 0, 'students_paid' => 0, 'avg_receipt' => 0,
                    'online_paid' => 0, 'online_pending' => 0, 'discount' => 0, 'fine' => 0, 'net' => 0,
                ],
                'collectionsByDay' => ['labels'=>[], 'data'=>[]],
                'cumulativeByDay'  => ['labels'=>[], 'data'=>[]],
                'topHeads'         => [],
                'topHeadsTable'    => [],
                'headShare'        => ['labels'=>[], 'data'=>[]],
                'paymentMethods'   => ['labels'=>[], 'data'=>[]],
                'methodsByDay'     => ['labels'=>[], 'datasets'=>[]],
                'modeSplitByDay'   => ['labels'=>[], 'online'=>[], 'offline'=>[]],
                'onlineTimeline'   => ['labels'=>[], 'data'=>[]],
                'onlineStatus'     => ['labels'=>[], 'data'=>[]],
                'recentReceipts'   => [],
                'error'            => 'Failed to build summary.',
            ], 200);
        }
    }

    /* =========================
       Helpers
    ==========================*/

    private function sanitizeDates($from, $to)
    {
        $today = Carbon::today();
        $from  = $from ? Carbon::parse($from) : $today->copy()->subDays(29);
        $to    = $to   ? Carbon::parse($to)   : $today;
        if ($from->gt($to)) { $tmp=$from; $from=$to; $to=$tmp; }
        return [$from->toDateString(), $to->toDateString()];
    }

    private function qcol($table, $col)
    {
        if (!$col) return null;
        return (strpos($col, '.') !== false) ? $col : ($table.'.'.$col);
    }

    private function feeCollectionColumns()
    {
        $date   = Schema::hasColumn('fee_collections', 'date') ? 'date' : 'created_at';
        $amount = 'amount';
        foreach (['paid_amount','payment_amount','net_amount','total','amount'] as $c) {
            if (Schema::hasColumn('fee_collections', $c)) { $amount = $c; break; }
        }

        $headId = null;
        foreach (['fee_head_id','head_id','fee_heads_id'] as $hc) {
            if (Schema::hasColumn('fee_collections', $hc)) { $headId = $hc; break; }
        }

        $student = null;
        foreach (['students_id','student_id','studentID'] as $c) {
            if (Schema::hasColumn('fee_collections', $c)) { $student = $c; break; }
        }

        $method  = Schema::hasColumn('fee_collections', 'payment_method') ? 'payment_method'
               : (Schema::hasColumn('fee_collections','payment_mode') ? 'payment_mode' : null);

        $receipt = Schema::hasColumn('fee_collections', 'receipt_no') ? 'receipt_no'
               : (Schema::hasColumn('fee_collections','reference') ? 'reference' : null);

        $masterId = Schema::hasColumn('fee_collections', 'fee_master_id') ? 'fee_master_id'
                 : (Schema::hasColumn('fee_collections', 'fee_masters_id') ? 'fee_masters_id' : null);

        return [$date, $amount, $headId, $student, $method, $receipt, $masterId];
    }

    private function onlinePaymentColumns()
    {
        $date   = Schema::hasColumn('online_payments', 'created_at') ? 'created_at' : 'date';
        $amount = Schema::hasColumn('online_payments', 'amount')     ? 'amount'     : 'charge_amount';
        $status = Schema::hasColumn('online_payments', 'payment_status') ? 'payment_status'
               : (Schema::hasColumn('online_payments', 'status') ? 'status' : null);
        $verify = Schema::hasColumn('online_payments', 'verified') ? 'verified' : null;

        $student = null;
        foreach (['students_id','student_id','studentID'] as $c) {
            if (Schema::hasColumn('online_payments', $c)) { $student = $c; break; }
        }
        return [$date, $amount, $status, $verify, $student];
    }

    /** Students table synonyms */
    private function studentsSynonym($wanted)
    {
        $map = [
            'faculty'           => ['faculty','faculty_id','program_id'],
            'faculty_id'        => ['faculty','faculty_id','program_id'],
            'semester'          => ['semester','semester_id','sem_id'],
            'semester_id'       => ['semester','semester_id','sem_id'],
            'student_batch_id'  => ['batch','student_batch_id','batch_id'],
            'batch'             => ['batch','student_batch_id','batch_id'],
            'department_id'     => ['department_id','dept_id'],
            'department_program'=> ['department_program_id','dept_program_id','program_map_id'],
        ];
        $cands = $map[$wanted] ?? [$wanted];
        foreach ($cands as $c) if (Schema::hasColumn('students', $c)) return $c;
        return null;
    }

    private function studentsRegNoColumn()
    {
        $cands = ['reg_no','reg_number','registration_no','register_no','regd_no','enrollment_no','enroll_no','roll_no','student_code'];
        foreach ($cands as $c) if (Schema::hasColumn('students', $c)) return $c;
        return null;
    }

    private function studentsNameColumns()
    {
        $name   = Schema::hasColumn('students', 'name') ? 'name' : null;
        $first  = Schema::hasColumn('students', 'first_name')  ? 'first_name'  : null;
        $middle = Schema::hasColumn('students', 'middle_name') ? 'middle_name' : null;
        $last   = Schema::hasColumn('students', 'last_name')   ? 'last_name'   : null;
        return [$name, $first, $middle, $last];
    }

    /** Base table synonyms */
    private function tableSynonym($table, $wanted)
    {
        $map = [
            'faculty'           => ['faculty','faculty_id','program_id'],
            'faculty_id'        => ['faculty','faculty_id','program_id'],
            'semester'          => ['semester','semester_id','sem_id'],
            'semester_id'       => ['semester','semester_id','sem_id'],
            'student_batch_id'  => ['batch','student_batch_id','batch_id'],
            'batch'             => ['batch','student_batch_id','batch_id'],
            'department_id'     => ['department_id','dept_id'],
            'department_program'=> ['department_program_id','dept_program_id','program_map_id'],
        ];
        $cands = $map[$wanted] ?? [$wanted];
        foreach ($cands as $c) if (Schema::hasColumn($table, $c)) return $c;
        return null;
    }

    private function hasJoin($q, $alias)
    {
        $joins = $q->getQuery()->joins;
        if (!is_array($joins)) return false;
        foreach ($joins as $j) {
            if (isset($j->table) && (
                strpos($j->table, ' '.$alias) !== false ||
                $j->table === $alias ||
                strpos($j->table, $alias.' ') !== false
            )) {
                return true;
            }
        }
        return false;
    }

    private function joinStudentsIfNeeded($q, $table, $studentFk, $filters)
    {
        if (!$studentFk) return false;
        if ($this->hasJoin($q, 'st')) return true;
        $q->leftJoin('students as st', $table.'.'.$studentFk, '=', 'st.id');
        return true;
    }

    private function joinDpMapByFaculty($q, $table, $studentsJoined)
    {
        if (!Schema::hasTable('department_programs')) return false;
        if ($this->hasJoin($q, 'dpmap')) return true;

        if ($studentsJoined) {
            $stFac = $this->studentsSynonym('faculty');
            if ($stFac) {
                $q->leftJoin('department_programs as dpmap', function($join) use ($stFac){
                    $join->on('dpmap.faculty_id', '=', 'st.'.$stFac);
                });
                return true;
            }
        }

        $baseFac = $this->tableSynonym($table, 'faculty');
        if ($baseFac) {
            $q->leftJoin('department_programs as dpmap', function($join) use ($table, $baseFac){
                $join->on('dpmap.faculty_id', '=', $table.'.'.$baseFac);
            });
            return true;
        }
        return false;
    }

    private function deptIdsFromHeadId($headId)
    {
        if (!$headId) return collect();
        $cands = [
            ['table'=>'department_heads_department',   'head'=>'department_head_id', 'dept'=>'department_id'],
            ['table'=>'department_head_departments',   'head'=>'department_head_id', 'dept'=>'department_id'],
            ['table'=>'department_head_department',    'head'=>'department_head_id', 'dept'=>'department_id'],
            ['table'=>'department_heads_departments',  'head'=>'department_head_id', 'dept'=>'department_id'],
            ['table'=>'department_department_head',    'head'=>'department_head_id', 'dept'=>'department_id'],
        ];
        foreach ($cands as $t) {
            if (Schema::hasTable($t['table'])
                && Schema::hasColumn($t['table'], $t['head'])
                && Schema::hasColumn($t['table'], $t['dept'])) {
                return DB::table($t['table'])->where($t['head'], $headId)->pluck('department_id');
            }
        }
        if (Schema::hasTable('departments') && Schema::hasColumn('departments','department_head_id')) {
            return DB::table('departments')->where('department_head_id', $headId)->pluck('id');
        }
        return collect();
    }

    /** Hierarchy: Department Head → Department → Faculty → Semester → Batch */
    private function applyHierarchy($q, $table, $filters, $studentFk)
    {
        $studentsJoined = $this->joinStudentsIfNeeded($q, $table, $studentFk, $filters);

        // Department
        if (!empty($filters['department_id'])) {
            $joined = $this->joinDpMapByFaculty($q, $table, $studentsJoined);
            if ($joined && Schema::hasColumn('department_programs', 'department_id')) {
                $q->where('dpmap.department_id', $filters['department_id']);
            } else {
                $baseDept = $this->tableSynonym($table, 'department_id');
                if ($baseDept) $q->where($table.'.'.$baseDept, $filters['department_id']);
            }
        }

        // Department Head
        if (!empty($filters['department_head_id'])) {
            $deptIds = $this->deptIdsFromHeadId($filters['department_head_id']);
            if ($deptIds->isNotEmpty()) {
                $joined = $this->joinDpMapByFaculty($q, $table, $studentsJoined);
                if ($joined && Schema::hasColumn('department_programs', 'department_id')) {
                    $q->whereIn('dpmap.department_id', $deptIds->all());
                } else {
                    $baseDept = $this->tableSynonym($table, 'department_id');
                    if ($baseDept) $q->whereIn($table.'.'.$baseDept, $deptIds->all());
                    else $q->whereRaw('1=0');
                }
            } else {
                $q->whereRaw('1=0');
            }
        }

        // Faculty
        if (!empty($filters['faculty_id'])) {
            $stFac = $this->studentsSynonym('faculty');
            $baseFac = $this->tableSynonym($table, 'faculty');
            if ($stFac)       $q->where('st.'.$stFac, $filters['faculty_id']);
            elseif ($baseFac) $q->where($table.'.'.$baseFac, $filters['faculty_id']);
        }

        // Semester
        if (!empty($filters['semester_id'])) {
            $stSem = $this->studentsSynonym('semester');
            $baseSem = $this->tableSynonym($table, 'semester');
            if ($stSem)       $q->where('st.'.$stSem, $filters['semester_id']);
            elseif ($baseSem) $q->where($table.'.'.$baseSem, $filters['semester_id']);
        }

        // Batch
        if (!empty($filters['student_batch_id'])) {
            $stBatch = $this->studentsSynonym('batch');
            $baseBatch = $this->tableSynonym($table, 'batch');
            if ($stBatch)       $q->where('st.'.$stBatch, $filters['student_batch_id']);
            elseif ($baseBatch) $q->where($table.'.'.$baseBatch, $filters['student_batch_id']);
        }
    }

    private function applyDateRange($q, $table, $dateCol, $from, $to)
    {
        if ($dateCol) {
            $q->whereDate($this->qcol($table, $dateCol), '>=', $from)
              ->whereDate($this->qcol($table, $dateCol), '<=', $to);
        }
    }

    private function sumWhereFlexible($q, $amountColQ, $statusColQ, $accepted)
    {
        if (!$statusColQ || !$amountColQ) return 0.0;
        $clone = (clone $q);
        $clone->where(function($w) use ($statusColQ, $accepted) {
            foreach ($accepted as $v) {
                $w->orWhere($statusColQ, $v)
                  ->orWhereRaw("LOWER($statusColQ) = ?", [strtolower((string)$v)]);
            }
        });
        return (float) $clone->sum($amountColQ);
    }

    /**
     * Build Top Heads aggregates with robust label resolution:
     * 1) fee_collections.fee_head_id → fee_heads.fee_head_title
     * 2) fee_masters.fee_head_id     → fee_heads.fee_head_title
     * 3) fee_masters.fee_head (text) → if numeric use fee_heads; else use text
     * 4) fee_collections.head / fee_collections.fee_head (text)
     *
     * Returns ['chart'=>[{label, value}], 'table'=>[{label, sum, count, avg}]].
     */
    private function buildTopHeadsAggregates($fc, $headIdCol, $headIdQ, $amountQ, $masterIdCol)
    {
        // A) Direct head id on fee_collections → join fee_heads for title
        if ($headIdCol && $headIdQ && Schema::hasTable('fee_heads')) {
            $rows = (clone $fc)
                ->leftJoin('fee_heads as fh', $headIdQ, '=', 'fh.id')
                ->select(
                    DB::raw("COALESCE(NULLIF(TRIM(fh.fee_head_title),''), CONCAT('Head #', $headIdQ)) as label"),
                    DB::raw("SUM($amountQ) as s"),
                    DB::raw("COUNT(*) as c")
                )
                ->whereNotNull($headIdQ)
                ->groupBy('label')
                ->orderByDesc('s')
                ->limit(10)
                ->get();

            if ($rows->count()) return $this->formatHeadRows($rows);
        }

        // B) Through fee_masters.fee_head_id → join fee_heads
        if ($masterIdCol && Schema::hasTable('fee_masters') && Schema::hasTable('fee_heads') && Schema::hasColumn('fee_masters','fee_head_id')) {
            $rows = (clone $fc)
                ->leftJoin('fee_masters as fm', 'fee_collections.'.$masterIdCol, '=', 'fm.id')
                ->leftJoin('fee_heads as fh', 'fm.fee_head_id', '=', 'fh.id')
                ->select(
                    DB::raw("COALESCE(NULLIF(TRIM(fh.fee_head_title),''), CONCAT('Head #', fm.fee_head_id)) as label"),
                    DB::raw("SUM($amountQ) as s"),
                    DB::raw("COUNT(*) as c")
                )
                ->whereNotNull('fm.fee_head_id')
                ->groupBy('label')
                ->orderByDesc('s')
                ->limit(10)
                ->get();

            if ($rows->count()) return $this->formatHeadRows($rows);
        }

        // C) fee_masters.fee_head (text). If looks numeric, try map to fee_heads; else use text
        if ($masterIdCol && Schema::hasTable('fee_masters') && Schema::hasColumn('fee_masters','fee_head')) {
            // First try: interpret as numeric id and join fee_heads
            $tryNumeric = (clone $fc)
                ->leftJoin('fee_masters as fm', 'fee_collections.'.$masterIdCol, '=', 'fm.id');

            if (Schema::hasTable('fee_heads')) {
                $numericRows = (clone $tryNumeric)
                    ->leftJoin('fee_heads as fh', DB::raw("CAST(NULLIF(fm.fee_head,'') AS UNSIGNED)"), '=', 'fh.id')
                    ->select(
                        DB::raw("COALESCE(NULLIF(TRIM(fh.fee_head_title),''), NULL) as label"),
                        DB::raw("SUM($amountQ) as s"),
                        DB::raw("COUNT(*) as c")
                    )
                    ->whereRaw("fm.fee_head REGEXP '^[0-9]+$'")
                    ->groupBy('label')
                    ->orderByDesc('s')
                    ->limit(10)
                    ->get()
                    ->filter(fn($r)=> !empty($r->label)); // keep only ones we actually mapped

                if ($numericRows->count()) return $this->formatHeadRows($numericRows);
            }

            // Fallback: treat fee_masters.fee_head as the label itself
            $rows = (clone $fc)
                ->leftJoin('fee_masters as fm', 'fee_collections.'.$masterIdCol, '=', 'fm.id')
                ->select(
                    DB::raw("COALESCE(NULLIF(TRIM(fm.fee_head),''),'—') as label"),
                    DB::raw("SUM($amountQ) as s"),
                    DB::raw("COUNT(*) as c")
                )
                ->groupBy('label')
                ->orderByDesc('s')
                ->limit(10)
                ->get();

            if ($rows->count()) return $this->formatHeadRows($rows);
        }

        // D) Textual head on fee_collections (head / fee_head)
        foreach (['head','fee_head'] as $alt) {
            if (Schema::hasColumn('fee_collections', $alt)) {
                $altQ = 'fee_collections.'.$alt;
                $rows = (clone $fc)
                    ->select(
                        DB::raw("COALESCE(NULLIF(TRIM($altQ),''),'—') as label"),
                        DB::raw("SUM($amountQ) as s"),
                        DB::raw("COUNT(*) as c")
                    )
                    ->groupBy('label')
                    ->orderByDesc('s')
                    ->limit(10)
                    ->get();

                if ($rows->count()) return $this->formatHeadRows($rows);
            }
        }

        return ['chart'=>[], 'table'=>[]];
    }

    /** Format helpers for Top Heads */
    private function formatHeadRows($rows)
    {
        $chart=[]; $table=[];
        foreach ($rows as $r) {
            $sum=(float)$r->s; $cnt=(int)$r->c; $avg=$cnt?round($sum/$cnt,2):0.0;
            $label = $r->label ?: '—';
            $chart[]=['label'=>$label,'value'=>$sum];
            $table[]=['label'=>$label,'sum'=>$sum,'count'=>$cnt,'avg'=>$avg];
        }
        return ['chart'=>$chart,'table'=>$table];
    }

    private function normalizePM($v)
    {
        $t = strtoupper(trim((string)$v));
        if ($t === '' || $t === null) return 'Unknown';
        if (strpos($t,'CASH') !== false) return 'Cash';
        if (strpos($t,'BANK') !== false) return 'Bank';
        if (strpos($t,'ONLINE') !== false || strpos($t,'SSL') !== false || strpos($t,'UPAY') !== false || strpos($t,'UCB') !== false) return 'Online';
        if (strpos($t,'CARD') !== false || strpos($t,'VISA') !== false || strpos($t,'MASTER') !== false) return 'Card';
        if (strpos($t,'CHEQUE') !== false || strpos($t,'CHECK') !== false) return 'Cheque';
        return ucwords(strtolower($t));
    }

    private function buildPaymentMethods($fc, $methodCol, $methodQ, $amountQ)
{
    if (!$methodCol || !$methodQ) return ['labels'=>[], 'data'=>[]];

    // group by raw method in SQL
    $rows = (clone $fc)
        ->select(DB::raw("$methodQ as pm"), DB::raw("SUM($amountQ) as s"))
        ->groupBy('pm')
        ->get();

    // THEN consolidate by normalized label in PHP
    $bucket = [];
    foreach ($rows as $r) {
        $label = $this->normalizePM($r->pm);
        $bucket[$label] = ($bucket[$label] ?? 0) + (float)$r->s;
    }

    // sort desc by total for consistent ordering
    arsort($bucket, SORT_NUMERIC);

    return [
        'labels' => array_keys($bucket),
        'data'   => array_values($bucket),
    ];
}


    private function buildPaymentMethodsByDay($fc, $fcDateQ, $amountQ, $methodQ)
    {
        if (!$methodQ) return ['labels'=>[], 'datasets'=>[]];

        $rows = (clone $fc)
            ->select(DB::raw("DATE($fcDateQ) as d"), DB::raw("$methodQ as pm"), DB::raw("SUM($amountQ) as s"))
            ->groupBy(DB::raw("DATE($fcDateQ)"), 'pm')
            ->orderBy(DB::raw("DATE($fcDateQ)"))
            ->get();

        $labels = $rows->pluck('d')->unique()->values()->all();
        $pmGroups = [];

        foreach ($rows as $r) {
            $label = $this->normalizePM($r->pm);
            if (!isset($pmGroups[$label])) {
                $pmGroups[$label] = [];
                foreach ($labels as $d) $pmGroups[$label][$d] = 0.0;
            }
            $pmGroups[$label][$r->d] = (float)$r->s;
        }

        $datasets = [];
        foreach ($pmGroups as $label => $dateMap) {
            $datasets[] = ['label' => $label, 'data' => array_values($dateMap)];
        }
        return ['labels'=>$labels, 'datasets'=>$datasets];
    }

    private function buildModeSplitByDay($fc, $fcDateQ, $amountQ, $methodQ)
    {
        $rows = (clone $fc)
            ->select(DB::raw("DATE($fcDateQ) as d"),
                     DB::raw($methodQ ? "$methodQ as pm" : "'' as pm"),
                     DB::raw("SUM($amountQ) as s"))
            ->groupBy(DB::raw("DATE($fcDateQ)"), 'pm')
            ->orderBy(DB::raw("DATE($fcDateQ)"))
            ->get();

        $labels = $rows->pluck('d')->unique()->values()->all();
        $online  = []; $offline = [];
        foreach ($labels as $d) { $online[$d]=0.0; $offline[$d]=0.0; }

        foreach ($rows as $r) {
            $pm = $this->normalizePM($r->pm);
            if ($pm === 'Online') $online[$r->d] += (float)$r->s;
            else $offline[$r->d] += (float)$r->s;
        }

        return [
            'labels'  => $labels,
            'online'  => array_values($online),
            'offline' => array_values($offline),
        ];
    }

    private function onlineUniqueIdColumn()
    {
        foreach (['transaction_id','tran_id','reference','ref','invoice_no','order_id','uuid','id'] as $c) {
            if (Schema::hasColumn('online_payments', $c)) {
                $q = (strpos($c, '.') !== false) ? $c : ('online_payments.'.$c);
                return [$c, $q];
            }
        }
        return ['id', 'online_payments.id'];
    }

    private function buildOnlineStatusPie($op, $statusCol, $statusQ, $verifyCol, $verifyQ)
    {
        if ($verifyCol && $verifyQ) {
            $rows = (clone $op)
                ->select(DB::raw("$verifyQ as v"), DB::raw("COUNT(*) as c"))
                ->groupBy('v')->get();

            $labels=[]; $data=[];
            foreach ($rows as $r) {
                $labels[] = ((string)$r->v === '1' || strtolower((string)$r->v)==='true') ? 'Verified' : 'Pending';
                $data[]   = (int)$r->c;
            }
            return ['labels'=>$labels, 'data'=>$data];
        }

        if ($statusCol && $statusQ) {
            $rows = (clone $op)
                ->select(DB::raw("$statusQ as s"), DB::raw("COUNT(*) as c"))
                ->groupBy('s')->get();

            $map = [
                'PAID'=>'Paid', 'SUCCESS'=>'Paid', '1'=>'Paid', 'TRUE'=>'Paid',
                'PENDING'=>'Pending','0'=>'Pending','INIT'=>'Pending','PROCESSING'=>'Pending',
                'FAILED'=>'Failed', 'CANCELED'=>'Canceled', 'CANCELLED'=>'Canceled',
                'SUSPECT'=>'Suspected', 'HOLD'=>'On Hold',
            ];

            $bucket = [];
            foreach ($rows as $r) {
                $k = strtoupper((string)$r->s);
                $label = $map[$k] ?? $k;
                $bucket[$label] = ($bucket[$label] ?? 0) + (int)$r->c;
            }
            return ['labels'=>array_keys($bucket), 'data'=>array_values($bucket)];
        }

        return ['labels'=>[], 'data'=>[]];
    }

    private function findNoteColumn()
    {
        foreach (['note','notes','remark','remarks','description'] as $c) {
            if (Schema::hasColumn('fee_collections', $c)) return $c;
        }
        return null;
    }

    private function formatRecentReceiptsDetailed($rows, $fcDateCol, $fcAmountCol, $fcMethodCol)
    {
        $discCol = Schema::hasColumn('fee_collections','discount') ? 'discount' : null;
        $fineCol = Schema::hasColumn('fee_collections','fine')     ? 'fine'     : null;
        $noteCol = $this->findNoteColumn();

        $out = [];
        foreach ($rows as $r) {
            // student name
            $name = '';
            if (isset($r->st_name) && $r->st_name) {
                $name = $r->st_name;
            } else {
                $parts = [];
                foreach (['st_first_name','st_middle_name','st_last_name'] as $k) {
                    if (isset($r->{$k}) && trim((string)$r->{$k}) !== '') $parts[] = trim((string)$r->{$k});
                }
                $name = $parts ? implode(' ', $parts) : '—';
            }

            $out[] = [
                'reg_no'         => $r->st_reg_no ?? '—',
                'student'        => $name,
                'date'           => $r->{$fcDateCol},
                'payment_method' => $fcMethodCol ? ($r->{$fcMethodCol} ?? '') : '',
                'amount'         => (float)($r->{$fcAmountCol} ?? 0),
                'discount'       => (float)($discCol ? ($r->{$discCol} ?? 0) : 0),
                'fine'           => (float)($fineCol ? ($r->{$fineCol} ?? 0) : 0),
                'note'           => $noteCol ? ($r->{$noteCol} ?? '') : '',
            ];
        }
        return $out;
    }
}
