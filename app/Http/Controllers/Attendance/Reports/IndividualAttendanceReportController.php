<?php

namespace App\Http\Controllers\Attendance\Reports;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class IndividualAttendanceReportController extends CollegeBaseController
{
    protected $base_route = '';
    protected $view_path  = 'attendance.reports.individual';
    protected $panel      = 'Individual Attendance Card';

    /* ============================== Pages ============================== */

    public function studentIndex(Request $request)
    {
        return $this->indexBase('student');
    }

    public function staffIndex(Request $request)
    {
        return $this->indexBase('staff');
    }

    private function indexBase(string $kind)
    {
        try {
            $data = [
                'panel'  => $this->panel,
                'kind'   => $kind, // 'student' | 'staff'
                'years'  => $this->yearOptions(8),
                'months' => $this->monthOptions(),
            ];
            return view(parent::loadDataToView($this->view_path.'.index'), $data);
        } catch (\Throwable $e) {
            Log::error('IndividualCard@index failed', ['err'=>$e->getMessage()]);
            return back()->with('message_danger', 'Failed to load Individual Attendance Card.');
        }
    }

    /* ======================== Typeahead Search ======================== */
    // GET /attendance/reports/individual/search?kind=student|staff&q=term
    public function search(Request $request)
    {
        try {
            $kind = strtolower($request->get('kind',''));
            $q    = trim((string)$request->get('q',''));
            if (!$q || !in_array($kind, ['student','staff'], true)) {
                return response()->json(['ok'=>true, 'rows'=>[]]);
            }

            if ($kind === 'student') {
                if (!Schema::hasTable('students')) return response()->json(['ok'=>true, 'rows'=>[]]);

                $regCol = $this->firstExistingColumn('students', [
                    'reg_no','reg_number','registration_no','register_no',
                    'regd_no','enrollment_no','enroll_no','roll_no','student_code'
                ]);
                $name   = $this->firstExistingColumn('students', ['name','full_name','display_name']);
                $first  = $this->firstExistingColumn('students', ['first_name']);
                $middle = $this->firstExistingColumn('students', ['middle_name']);
                $last   = $this->firstExistingColumn('students', ['last_name']);

                $sel = ['id'];
                if ($regCol) $sel[] = "students.$regCol as reg_no";
                if ($name)   $sel[] = "students.$name as st_name";
                if ($first)  $sel[] = "students.$first as st_first";
                if ($middle) $sel[] = "students.$middle as st_middle";
                if ($last)   $sel[] = "students.$last as st_last";

                $rows = DB::table('students')->select($sel)
                    ->when($regCol, fn($qq)=>$qq->orWhere("students.$regCol",'like',"%$q%"))
                    ->when($name,   fn($qq)=>$qq->orWhere("students.$name",'like',"%$q%"))
                    ->when($first,  fn($qq)=>$qq->orWhere("students.$first",'like',"%$q%"))
                    ->when($middle, fn($qq)=>$qq->orWhere("students.$middle",'like',"%$q%"))
                    ->when($last,   fn($qq)=>$qq->orWhere("students.$last",'like',"%$q%"))
                    ->limit(20)->get();

                $out = [];
                foreach ($rows as $r) {
                    $disp = $r->st_name ?? trim(implode(' ', array_filter([$r->st_first??null, $r->st_middle??null, $r->st_last??null])));
                    if (!$disp) $disp = 'Unknown';
                    $out[] = [
                        'id'    => $r->id,
                        'label' => $disp,
                        'hint'  => $regCol?($r->reg_no??''):'',
                        'reg_no'=> $regCol?($r->reg_no??''):null,
                    ];
                }
                return response()->json(['ok'=>true,'rows'=>$out]);
            }

            // Staff search
            $staffTable = $this->firstExistingTable(['staff','employees','users']);
            if (!$staffTable) return response()->json(['ok'=>true, 'rows'=>[]]);

            $idCol   = Schema::hasColumn($staffTable,'id') ? 'id' : null;
            $regCol  = $this->firstExistingColumn($staffTable, ['reg_no','code','employee_code','employee_id','staff_code']);
            $name    = $this->firstExistingColumn($staffTable, ['name','full_name','display_name']);
            $first   = $this->firstExistingColumn($staffTable, ['first_name','firstname','given_name']);
            $middle  = $this->firstExistingColumn($staffTable, ['middle_name','middlename']);
            $last    = $this->firstExistingColumn($staffTable, ['last_name','lastname','surname']);
            $desigFk = $this->firstExistingColumn($staffTable, ['designation','staff_designation_id','designation_id','role_id']);

            $sel = [];
            if ($idCol)   $sel[] = "t.$idCol as id";
            if ($regCol)  $sel[] = "t.$regCol as reg_no";
            if ($name)    $sel[] = "t.$name as st_name";
            if ($first)   $sel[] = "t.$first as st_first";
            if ($middle)  $sel[] = "t.$middle as st_middle";
            if ($last)    $sel[] = "t.$last as st_last";
            if ($desigFk) $sel[] = "t.$desigFk as desig_id";

            $qBase = DB::table("$staffTable as t")->select($sel)
                ->when($regCol, fn($qq)=>$qq->orWhere("t.$regCol",'like',"%$q%"))
                ->when($name,   fn($qq)=>$qq->orWhere("t.$name",'like',"%$q%"))
                ->when($first,  fn($qq)=>$qq->orWhere("t.$first",'like',"%$q%"))
                ->when($middle, fn($qq)=>$qq->orWhere("t.$middle",'like',"%$q%"))
                ->when($last,   fn($qq)=>$qq->orWhere("t.$last",'like',"%$q%"))
                ->limit(20)->get();

            // Map designation titles for hint
            $desigTitle = [];
            $desigTab = $this->firstExistingTable(['staff_designations','designations','roles']);
            $desigCol = $desigTab ? $this->firstExistingColumn($desigTab, ['title','name','designation']) : null;
            $desigId  = $desigTab ? $this->firstExistingColumn($desigTab, ['id','designation_id','role_id']) : null;

            $out = [];
            foreach ($qBase as $r) {
                $disp = $r->st_name ?? trim(implode(' ', array_filter([$r->st_first??null, $r->st_middle??null, $r->st_last??null])));
                if (!$disp) $disp='Unknown';

                $hintParts = [];
                if ($regCol && !empty($r->reg_no)) $hintParts[] = $r->reg_no;
                $dTxt = null;
                if ($desigFk && !empty($r->desig_id) && $desigTab && $desigCol && $desigId) {
                    if (!isset($desigTitle[$r->desig_id])) {
                        $got = DB::table($desigTab)->select("$desigCol as title")->where($desigId,$r->desig_id)->first();
                        $desigTitle[$r->desig_id] = $got->title ?? null;
                    }
                    $dTxt = $desigTitle[$r->desig_id] ?? null;
                }
                if ($dTxt) $hintParts[] = $dTxt;

                $out[] = [
                    'id'    => $r->id,
                    'label' => $disp,
                    'hint'  => implode(' • ', $hintParts),
                    'reg_no'=> $regCol?($r->reg_no??''):null,
                ];
            }
            return response()->json(['ok'=>true,'rows'=>$out]);

        } catch (\Throwable $e) {
            Log::error('IndividualCard@search failed', ['err'=>$e->getMessage()]);
            return response()->json(['ok'=>false,'rows'=>[],'msg'=>'Search failed']);
        }
    }

    /* ======================= Subject list (NEW) ======================= */
    // GET /attendance/reports/individual/subjects
    public function subjects(Request $request)
    {
        try {
            $tab = $this->firstExistingTable(['subjects','course_subjects','semester_subjects']);
            if (!$tab) return response()->json(['ok'=>true, 'rows'=>[]]);

            $idCol = $this->firstExistingColumn($tab, ['id','subject_id']);
            $nmCol = $this->firstExistingColumn($tab, ['title','name','subject','subject_title']);

            if (!$idCol || !$nmCol) return response()->json(['ok'=>true, 'rows'=>[]]);

            $q = trim((string)$request->get('q',''));
            $rows = DB::table($tab)->select([$idCol.' as id', $nmCol.' as name'])
                ->when($q !== '', fn($qq)=>$qq->where($nmCol,'like',"%$q%"))
                ->orderBy($nmCol)->limit(200)->get();

            $out=[]; foreach($rows as $r){ $out[]=['id'=>(int)$r->id,'name'=>$r->name?:('Subject #'.$r->id)]; }
            return response()->json(['ok'=>true,'rows'=>$out]);
        } catch (\Throwable $e) {
            Log::error('IndividualCard@subjects failed', ['err'=>$e->getMessage()]);
            return response()->json(['ok'=>false,'rows'=>[],'msg'=>'Failed to load subjects']);
        }
    }

    /* ========================== Data Provider ========================== */
    // GET /attendance/reports/individual/data
    // kind=student|staff, person_id=ID, period=monthly|yearly|custom|lifetime
    // month, year (monthly) | year (yearly) | date_from,date_to (custom)
    // report_type=regular|subject (students only), subject_id?
    public function data(Request $request)
    {
        try {
            $kind       = strtolower((string)$request->get('kind',''));
            $personId   = (int)$request->get('person_id', 0);
            $period     = strtolower((string)$request->get('period', 'monthly'));
            $month      = (int)$request->get('month', 0);
            $year       = (int)$request->get('year', 0);
            $dateFrom   = $request->get('date_from');
            $dateTo     = $request->get('date_to');
            $reportType = strtolower((string)$request->get('report_type', 'regular'));
            $subjectId  = (int)$request->get('subject_id', 0);

            if (!in_array($kind, ['student','staff'], true) || !$personId) {
                return response()->json(['ok'=>false,'msg'=>'Choose a person first.']);
            }

            $attTable = $this->firstExistingTable(['attendances','attendance','attendence']);
            if (!$attTable) return response()->json(['ok'=>false,'msg'=>'Attendance table not found.']);

            $statusTab = Schema::hasTable('attendance_statuses') ? 'attendance_statuses' : null;
            $statusCodeCol = $statusTab ? $this->firstExistingColumn($statusTab, ['code','short_code','symbol','abbr','title','name','status']) : null;
            $statusIdCol   = $statusTab ? $this->firstExistingColumn($statusTab, ['id','status_id']) : null;

            // base range
            [$start, $end] = $this->resolveRange($period, $year, $month, $dateFrom, $dateTo);

            // lifetime -> first actual attendance date for person/subject
            if ($period === 'lifetime') {
                $first = null;
                if ($kind==='student' && $reportType==='subject' && $subjectId>0) {
                    $first = $this->firstSubjectAttendanceDate($personId, $subjectId);
                } elseif ($kind==='student') {
                    $first = $this->firstStudentAttendanceDate($personId, $attTable);
                } else { // staff
                    $first = $this->firstStaffAttendanceDate($personId, $attTable);
                }
                if ($first) $start = Carbon::parse($first)->startOfDay();
            }

            $legend = $this->statusPalette();
            $colors = []; foreach ($legend as $l) $colors[$l['code']] = $l['color'];

            $person = ($kind==='student') ? $this->studentCardInfo($personId)
                                          : $this->staffCardInfo($personId);
            if (!$person) return response()->json(['ok'=>false,'msg'=>'Person not found.']);

            // ====== Build rows (date, status, in, out, total, mins) ======
            $subjectName = null;
            if ($kind==='student' && $reportType==='subject' && $subjectId>0) {
                $rows = $this->buildStudentSubjectRows($personId, $subjectId, $start, $end, $statusTab, $statusIdCol, $statusCodeCol);
                $rtype= 'Subject Attendance';
                $subjectName = $this->subjectTitleById($subjectId);
            } else {
                $rows = ($kind==='student')
                    ? $this->buildPolymorphicRows($personId, $start, $end, $statusTab, $statusIdCol, $statusCodeCol, $this->studentMorphTypes(), $attTable)
                    : $this->buildStaffRows($personId, $start, $end, $statusTab, $statusIdCol, $statusCodeCol, $attTable);
                $rtype= 'Regular Attendance';
            }

            // Totals by status + total mins
            $totals = ['P'=>0,'A'=>0,'L'=>0,'H'=>0,'LV'=>0,'EL'=>0];
            $totalMins = 0;
            foreach ($rows as $r) {
                $c = $this->normalizeStatus($r['status'] ?? '');
                if ($c && isset($totals[$c])) $totals[$c]++;
                $totalMins += (int)($r['mins'] ?? 0);
            }

            // Day grid (optional if small)
            $grid = [];
            if ($start->diffInDays($end) <= 62) {
                $map = []; foreach ($rows as $r) $map[$r['date']] = $this->normalizeStatus($r['status'] ?? '');
                $cursor = $start->copy();
                while ($cursor->lte($end)) {
                    $d = $cursor->toDateString();
                    $grid[] = ['date'=>$d, 'code'=>$map[$d] ?? ''];
                    $cursor->addDay();
                }
            }

            return response()->json([
                'ok'     => true,
                'type'   => $rtype,
                'kind'   => $kind,
                'person' => $person,
                'subject'=> $subjectId > 0 ? ['id'=>$subjectId, 'name'=>$subjectName] : null,
                'range'  => [
                    'start' => $start->toDateString(),
                    'end'   => $end->toDateString(),
                ],
                'legend'     => $legend,
                'colors'     => $colors,
                'rows'       => $rows,        // has in/out/total + mins
                'grid'       => $grid,        // optional fallback
                'totals'     => $totals,
                'total_mins' => $totalMins,
                'total_hm'   => $this->fmtHM($totalMins),
            ]);

        } catch (\Throwable $e) {
            Log::error('IndividualCard@data failed', [
                'err'=>$e->getMessage(),'line'=>$e->getLine(),'file'=>$e->getFile(),'req'=>$request->all()
            ]);
            return response()->json(['ok'=>false,'msg'=>'Failed to load attendance.']);
        }
    }

    /* ======================== Row Builders ========================= */

    // For student REGULAR attendance (polymorphic)
    private function buildPolymorphicRows(
        int $personId, Carbon $start, Carbon $end,
        $statusTab, $statusIdCol, $statusCodeCol, array $morphTypes, string $attTable
    ): array {
        $dateCol  = $this->firstExistingColumn($attTable, ['date','attendance_date','logged_date']);
        $aidCol   = $this->firstExistingColumn($attTable, ['attendable_id','student_id','person_id','user_id']);
        $atypeCol = $this->firstExistingColumn($attTable, ['attendable_type','person_type','user_type']);
        $stsCol   = $this->firstExistingColumn($attTable, ['attendance_status_id','status_id','status']);
        $inCol    = $this->firstExistingColumn($attTable, ['check_in_at','in_at','first_in_at']);
        $outCol   = $this->firstExistingColumn($attTable, ['check_out_at','out_at','last_out_at']);

        if (!$dateCol || !$aidCol || !$atypeCol || !$stsCol) return [];

        $q = DB::table($attTable)->select([
                DB::raw("DATE(`$attTable`.`$dateCol`) as d"),
                "$attTable.$inCol as in_at",
                "$attTable.$outCol as out_at",
            ])
            ->whereIn("$attTable.$atypeCol", $morphTypes)
            ->where("$attTable.$aidCol", $personId)
            ->whereDate("$attTable.$dateCol", '>=', $start->toDateString())
            ->whereDate("$attTable.$dateCol", '<=', $end->toDateString());

        if ($statusTab && $statusIdCol && $statusCodeCol) {
            $q->addSelect(DB::raw("COALESCE(`$statusTab`.`$statusCodeCol`,'') as s"))
              ->leftJoin($statusTab, "$statusTab.$statusIdCol", '=', "$attTable.$stsCol");
        } else {
            $q->addSelect("$attTable.$stsCol as s");
        }

        $rows = $q->orderBy('d')->get();

        $out = [];
        foreach ($rows as $r) {
            [$in, $outT, $total, $mins] = $this->formatInOutTotal($r->in_at, $r->out_at, true);
            $out[] = [
                'date'   => $r->d,
                'status' => $this->normalizeStatus($r->s),
                'in'     => $in,
                'out'    => $outT,
                'total'  => $total,
                'mins'   => $mins,
            ];
        }
        return $out;
    }

    // For staff: morph if possible; else match by attendance.reg_no == staff.reg_no
    private function buildStaffRows(
        int $staffId, Carbon $start, Carbon $end,
        $statusTab, $statusIdCol, $statusCodeCol, string $attTable
    ): array {
        $dateCol  = $this->firstExistingColumn($attTable, ['date','attendance_date','logged_date']);
        $aidCol   = $this->firstExistingColumn($attTable, ['attendable_id','staff_id','person_id','user_id']);
        $atypeCol = $this->firstExistingColumn($attTable, ['attendable_type','person_type','user_type']);
        $stsCol   = $this->firstExistingColumn($attTable, ['attendance_status_id','status_id','status']);
        $inCol    = $this->firstExistingColumn($attTable, ['check_in_at','in_at','first_in_at']);
        $outCol   = $this->firstExistingColumn($attTable, ['check_out_at','out_at','last_out_at']);
        $attReg   = $this->firstExistingColumn($attTable, ['reg_no','registration_no','code','employee_code','staff_code']);

        // Try morph first
        if ($dateCol && $aidCol && $atypeCol && $stsCol) {
            $morphRows = $this->buildPolymorphicRows($staffId, $start, $end, $statusTab, $statusIdCol, $statusCodeCol, $this->staffMorphTypes(), $attTable);
            if ($morphRows) return $morphRows;
        }

        // Fallback: reg_no match
        if (!$dateCol || !$attReg || !$stsCol) return [];
        $sInfo  = $this->staffCardInfo($staffId);
        $regVal = $sInfo['reg_no'] ?? null;
        if (!$regVal) return [];

        $q = DB::table($attTable)->select([
                DB::raw("DATE(`$attTable`.`$dateCol`) as d"),
                "$attTable.$inCol as in_at",
                "$attTable.$outCol as out_at",
            ])
            ->where("$attTable.$attReg", $regVal)
            ->whereDate("$attTable.$dateCol", '>=', $start->toDateString())
            ->whereDate("$attTable.$dateCol", '<=', $end->toDateString());

        if ($statusTab && $statusIdCol && $statusCodeCol) {
            $q->addSelect(DB::raw("COALESCE(`$statusTab`.`$statusCodeCol`,'') as s"))
              ->leftJoin($statusTab, "$statusTab.$statusIdCol", '=', "$attTable.$stsCol");
        } else {
            $q->addSelect("$attTable.$stsCol as s");
        }

        $rows = $q->orderBy('d')->get();

        $out = [];
        foreach ($rows as $r) {
            [$in, $outT, $total, $mins] = $this->formatInOutTotal($r->in_at, $r->out_at, true);
            $out[] = [
                'date'   => $r->d,
                'status' => $this->normalizeStatus($r->s),
                'in'     => $in,
                'out'    => $outT,
                'total'  => $total,
                'mins'   => $mins,
            ];
        }
        return $out;
    }

    // For student SUBJECT attendance
    private function buildStudentSubjectRows(
        int $studentId, int $subjectId, Carbon $start, Carbon $end,
        $statusTab, $statusIdCol, $statusCodeCol
    ): array {
        $tab = $this->firstExistingTable(['subject_attendances','attendance_subjects','student_subject_attendances','semester_subject_attendances']);
        if (!$tab) return [];

        $dateCol = $this->firstExistingColumn($tab, ['date','attendance_date']);
        $sidCol  = $this->firstExistingColumn($tab, ['student_id']);
        $subCol  = $this->firstExistingColumn($tab, ['subject_id','subjects_id']);
        $stsCol  = $this->firstExistingColumn($tab, ['attendance_status_id','status_id','status']);
        $inCol   = $this->firstExistingColumn($tab, ['in_at','check_in_at']);
        $outCol  = $this->firstExistingColumn($tab, ['out_at','check_out_at']);

        if (!$dateCol || !$sidCol || !$subCol || !$stsCol) return [];

        $q = DB::table($tab)->select([
                DB::raw("DATE(`$tab`.`$dateCol`) as d"),
                "$tab.$inCol as in_at",
                "$tab.$outCol as out_at",
            ])
            ->where("$tab.$sidCol", $studentId)
            ->where("$tab.$subCol", $subjectId)
            ->whereDate("$tab.$dateCol", '>=', $start->toDateString())
            ->whereDate("$tab.$dateCol", '<=', $end->toDateString());

        if ($statusTab && $statusIdCol && $statusCodeCol) {
            $q->addSelect(DB::raw("COALESCE(`$statusTab`.`$statusCodeCol`,'') as s"))
              ->leftJoin($statusTab, "$statusTab.$statusIdCol", '=', "$tab.$stsCol");
        } else {
            $q->addSelect("$tab.$stsCol as s");
        }

        $rows = $q->orderBy('d')->get();

        $out = [];
        foreach ($rows as $r) {
            [$in, $outT, $total, $mins] = $this->formatInOutTotal($r->in_at, $r->out_at, true);
            $out[] = [
                'date'   => $r->d,
                'status' => $this->normalizeStatus($r->s),
                'in'     => $in,
                'out'    => $outT,
                'total'  => $total,
                'mins'   => $mins,
            ];
        }
        return $out;
    }

    /* ============================ Info ============================ */

    private function studentCardInfo(int $id): ?array
    {
        if (!Schema::hasTable('students')) return null;
        $reg = $this->firstExistingColumn('students', [
            'reg_no','reg_number','registration_no','register_no','regd_no','enrollment_no','enroll_no','roll_no','student_code'
        ]);
        $name   = $this->firstExistingColumn('students',['name','full_name','display_name']);
        $first  = $this->firstExistingColumn('students',['first_name']);
        $middle = $this->firstExistingColumn('students',['middle_name']);
        $last   = $this->firstExistingColumn('students',['last_name']);

        $sel = ['id'];
        if ($reg)   $sel[] = "students.$reg as reg_no";
        if ($name)  $sel[] = "students.$name as st_name";
        if ($first) $sel[] = "students.$first as st_first";
        if ($middle)$sel[] = "students.$middle as st_middle";
        if ($last)  $sel[] = "students.$last as st_last";

        $r = DB::table('students')->select($sel)->where('id',$id)->first();
        if (!$r) return null;

        $disp = $r->st_name ?? trim(implode(' ', array_filter([$r->st_first??null,$r->st_middle??null,$r->st_last??null])));
        return [
            'id'    => $r->id,
            'name'  => $disp ?: '—',
            'reg_no'=> $reg ? ($r->reg_no ?? null) : null,
        ];
    }

    private function staffCardInfo(int $id): ?array
    {
        $tab = $this->firstExistingTable(['staff','employees','users']);
        if (!$tab) return null;
        $idCol  = $this->firstExistingColumn($tab, ['id']);
        $regCol = $this->firstExistingColumn($tab, ['reg_no','code','employee_code','employee_id','staff_code']);
        $name   = $this->firstExistingColumn($tab, ['name','full_name','display_name']);
        $first  = $this->firstExistingColumn($tab, ['first_name','firstname','given_name']);
        $middle = $this->firstExistingColumn($tab, ['middle_name','middlename']);
        $last   = $this->firstExistingColumn($tab, ['last_name','lastname','surname']);
        $desig  = $this->firstExistingColumn($tab, ['designation','staff_designation_id','designation_id','role_id']);

        $sel = [];
        if ($idCol)  $sel[] = "t.$idCol as id";
        if ($regCol) $sel[] = "t.$regCol as reg_no";
        if ($name)   $sel[] = "t.$name as st_name";
        if ($first)  $sel[] = "t.$first as st_first";
        if ($middle) $sel[] = "t.$middle as st_middle";
        if ($last)   $sel[] = "t.$last as st_last";
        if ($desig)  $sel[] = "t.$desig as desig_id";

        $r = DB::table("$tab as t")->select($sel)->where("t.$idCol",$id)->first();
        if (!$r) return null;

        // designation title
        $desTitle = null;
        if ($desig && !empty($r->desig_id)) {
            $dTab = $this->firstExistingTable(['staff_designations','designations','roles']);
            $dId  = $dTab ? $this->firstExistingColumn($dTab, ['id','designation_id','role_id']) : null;
            $dNm  = $dTab ? $this->firstExistingColumn($dTab, ['title','name','designation']) : null;
            if ($dTab && $dId && $dNm) {
                $d = DB::table($dTab)->select("$dNm as title")->where($dId,$r->desig_id)->first();
                $desTitle = $d->title ?? null;
            }
        }

        $disp = $r->st_name ?? trim(implode(' ', array_filter([$r->st_first??null,$r->st_middle??null,$r->st_last??null])));

        return [
            'id'         => $r->id,
            'name'       => $disp ?: '—',
            'reg_no'     => $regCol ? ($r->reg_no ?? null) : null,
            'designation'=> $desTitle,
        ];
    }

    /* ============================ Utils ============================ */

    private function resolveRange(string $period, int $year, int $month, $from, $to): array
    {
        $now = Carbon::now();
        switch ($period) {
            case 'yearly':
                $y = $year ?: (int)date('Y');
                return [Carbon::create($y,1,1)->startOfDay(), Carbon::create($y,12,31)->endOfDay()];
            case 'custom':
                $s = $from ? Carbon::parse($from)->startOfDay() : $now->copy()->startOfMonth();
                $e = $to   ? Carbon::parse($to)->endOfDay()   : $now->copy()->endOfMonth()->endOfDay();
                if ($e->lt($s)) [$s,$e] = [$e,$s];
                return [$s,$e];
            case 'lifetime':
                // temporary default; will be tightened to first actual attendance date in data()
                return [Carbon::create(2000,1,1)->startOfDay(), $now->copy()->endOfDay()];
            case 'monthly':
            default:
                $y = $year ?: (int)date('Y');
                $m = $month ?: (int)date('n');
                $s = Carbon::create($y,$m,1)->startOfDay();
                return [$s, $s->copy()->endOfMonth()->endOfDay()];
        }
    }

    private function formatInOutTotal($inRaw, $outRaw, bool $withMins=false): array
    {
        $in  = $inRaw  ? Carbon::parse($inRaw)  : null;
        $out = $outRaw ? Carbon::parse($outRaw) : null;

        $inStr  = $in  ? $in->format('H:i') : '';
        $outStr = $out ? $out->format('H:i') : '';
        $total  = '';
        $mins   = 0;

        if ($in && $out && $out->gte($in)) {
            $mins = $out->diffInMinutes($in);
            $total = sprintf('%02d:%02d', intdiv($mins,60), $mins%60);
        }

        return $withMins ? [$inStr, $outStr, $total, $mins] : [$inStr, $outStr, $total];
    }

    private function yearOptions($back = 5)
    {
        $y=(int)date('Y'); $out=[]; for($i=0;$i<$back;$i++) $out[]=$y-$i; return $out;
    }

    private function monthOptions()
    {
        $out=[]; for($m=1;$m<=12;$m++) $out[$m]=date('F',mktime(0,0,0,$m,10)); return $out;
    }

    private function normalizeStatus($v)
    {
        $t=strtoupper(trim((string)$v));
        if($t==='P'||str_contains($t,'PRESENT')) return 'P';
        if($t==='A'||str_contains($t,'ABSENT'))  return 'A';
        if($t==='L'||str_contains($t,'LATE'))    return 'L';
        if($t==='HL') return 'HL';
        if($t==='LV'||str_contains($t,'LEAVE'))  return 'LV';
        if($t==='EL'||str_contains($t,'EXCUSED'))return 'EL';
        if($t==='H'||str_contains($t,'HOLIDAY')) return 'H';
        if($t==='1'||$t==='TRUE') return 'P';
        if($t==='0'||$t==='FALSE') return 'A';
        return '';
    }

    private function statusPalette(): array
    {
        if (Schema::hasTable('attendance_statuses')) {
            $rows = DB::table('attendance_statuses')
                ->select(['code','label','color','order'])
                ->orderByRaw('COALESCE(`order`, 999)')
                ->get();
            $out=[];
            foreach($rows as $r){
                $out[]=[
                    'code'=>strtoupper((string)$r->code),
                    'label'=>$r->label ?: strtoupper((string)$r->code),
                    'color'=>$r->color ?: '#111827',
                ];
            }
            // Ensure common
            foreach ([['H','Holiday','#0EA5E9'],['LV','Leave','#7C3AED'],['EL','Excused','#3B82F6']] as $e) {
                [$c,$lbl,$clr] = $e;
                if(!collect($out)->pluck('code')->contains($c)) $out[]=['code'=>$c,'label'=>$lbl,'color'=>$clr];
            }
            return $out;
        }
        return [
            ['code'=>'P','label'=>'Present','color'=>'#10B981'],
            ['code'=>'A','label'=>'Absent','color'=>'#EF4444'],
            ['code'=>'L','label'=>'Late','color'=>'#F59E0B'],
            ['code'=>'EL','label'=>'Excused','color'=>'#3B82F6'],
            ['code'=>'HL','label'=>'Half-Leave','color'=>'#7C3AED'],
            ['code'=>'LV','label'=>'Leave','color'=>'#7C3AED'],
            ['code'=>'H','label'=>'Holiday','color'=>'#0EA5E9'],
        ];
    }

    private function studentMorphTypes(): array
    {
        $cands = ['App\\Models\\Student','App\\Student','student','Student'];
        if (class_exists('App\\Models\\Student')) array_unshift($cands,'App\\Models\\Student');
        elseif (class_exists('App\\Student'))     array_unshift($cands,'App\\Student');
        return array_values(array_unique($cands));
    }

    private function staffMorphTypes(): array
    {
        $cands = ['App\\Models\\Staff','App\\Staff','staff','Staff','App\\Models\\Employee','App\\Employee','employee','Employee'];
        if (class_exists('App\\Models\\Staff')) array_unshift($cands,'App\\Models\\Staff');
        elseif (class_exists('App\\Staff'))     array_unshift($cands,'App\\Staff');
        return array_values(array_unique($cands));
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

    private function subjectTitleById(int $id): ?string
    {
        $tab = $this->firstExistingTable(['subjects','course_subjects','semester_subjects']);
        if (!$tab) return null;
        $idCol = $this->firstExistingColumn($tab, ['id','subject_id']);
        $nmCol = $this->firstExistingColumn($tab, ['title','name','subject','subject_title']);
        if (!$idCol || !$nmCol) return null;
        $r = DB::table($tab)->select("$nmCol as name")->where($idCol, $id)->first();
        return $r ? ($r->name ?: null) : null;
    }

    private function fmtHM($mins): string
    {
        $m = max(0, (int) $mins);
        $h = intdiv($m, 60);
        $r = $m % 60;
        return $h . 'h ' . str_pad((string) $r, 2, '0', STR_PAD_LEFT) . 'm';
    }

    /* ======= Earliest attendance date (for lifetime) ======= */

    private function firstStudentAttendanceDate(int $studentId, string $attTable): ?string
    {
        $dateCol  = $this->firstExistingColumn($attTable, ['date','attendance_date','logged_date']);
        if (!$dateCol) return null;

        $aidCol   = $this->firstExistingColumn($attTable, ['attendable_id','student_id','person_id','user_id']);
        $atypeCol = $this->firstExistingColumn($attTable, ['attendable_type','person_type','user_type']);

        // Prefer morph
        if ($aidCol && $atypeCol) {
            $row = DB::table($attTable)
                ->whereIn($atypeCol, $this->studentMorphTypes())
                ->where($aidCol, $studentId)
                ->orderBy($dateCol, 'asc')
                ->select($dateCol.' as d')->first();
            if ($row && $row->d) return substr($row->d,0,10);
        }

        // Fallback direct student_id
        $sidCol = $this->firstExistingColumn($attTable, ['student_id']);
        if ($sidCol) {
            $row = DB::table($attTable)
                ->where($sidCol, $studentId)
                ->orderBy($dateCol, 'asc')->select($dateCol.' as d')->first();
            if ($row && $row->d) return substr($row->d,0,10);
        }
        return null;
    }

    private function firstStaffAttendanceDate(int $staffId, string $attTable): ?string
    {
        $dateCol  = $this->firstExistingColumn($attTable, ['date','attendance_date','logged_date']);
        if (!$dateCol) return null;

        $aidCol   = $this->firstExistingColumn($attTable, ['attendable_id','staff_id','person_id','user_id']);
        $atypeCol = $this->firstExistingColumn($attTable, ['attendable_type','person_type','user_type']);

        // Prefer morph
        if ($aidCol && $atypeCol) {
            $row = DB::table($attTable)
                ->whereIn($atypeCol, $this->staffMorphTypes())
                ->where($aidCol, $staffId)
                ->orderBy($dateCol, 'asc')
                ->select($dateCol.' as d')->first();
            if ($row && $row->d) return substr($row->d,0,10);
        }

        // Fallback reg_no match
        $regCol = $this->firstExistingColumn($attTable, ['reg_no','registration_no','code','employee_code','staff_code']);
        if ($regCol) {
            $info = $this->staffCardInfo($staffId);
            $reg  = $info['reg_no'] ?? null;
            if ($reg) {
                $row = DB::table($attTable)->where($regCol,$reg)
                    ->orderBy($dateCol,'asc')->select($dateCol.' as d')->first();
                if ($row && $row->d) return substr($row->d,0,10);
            }
        }
        return null;
    }

    private function firstSubjectAttendanceDate(int $studentId, int $subjectId): ?string
    {
        $tab = $this->firstExistingTable(['subject_attendances','attendance_subjects','student_subject_attendances','semester_subject_attendances']);
        if (!$tab) return null;
        $dateCol = $this->firstExistingColumn($tab, ['date','attendance_date']);
        $sidCol  = $this->firstExistingColumn($tab, ['student_id']);
        $subCol  = $this->firstExistingColumn($tab, ['subject_id','subjects_id']);
        if (!$dateCol || !$sidCol || !$subCol) return null;

        $row = DB::table($tab)
            ->where($sidCol,$studentId)
            ->where($subCol,$subjectId)
            ->orderBy($dateCol,'asc')
            ->select($dateCol.' as d')->first();

        return ($row && $row->d) ? substr($row->d,0,10) : null;
    }
}
