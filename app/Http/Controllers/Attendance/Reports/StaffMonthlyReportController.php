<?php

namespace App\Http\Controllers\Attendance\Reports;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class StaffMonthlyReportController extends CollegeBaseController
{
    protected $base_route = 'attendance.reports.staff.monthly';
    protected $view_path  = 'attendance.reports.staff';
    protected $panel      = 'Staff Monthly Attendance Report';

    /* ============================
       Page
       ============================ */
    public function index(Request $request)
    {
        try {
            return view(
                parent::loadDataToView($this->view_path.'.index'),
                [
                    'panel'         => $this->panel,
                    'years'         => $this->yearOptions(5),
                    'months'        => $this->monthOptions(),
                    'designations'  => $this->designationOptions(), // [{id,title}]
                ]
            );
        } catch (\Throwable $e) {
            Log::error('StaffMonthlyReport@index failed', ['err'=>$e->getMessage()]);
            return back()->with('message_danger', 'Failed to load Staff Monthly Attendance Report.');
        }
    }

    /* ============================
       JSON for grid
       ============================ */
    public function data(Request $request)
    {
        try {
            $year  = (int)$request->input('year');
            $month = (int)$request->input('month');
            $designationId = (int)$request->input('designation_id');
            $qStr = trim((string)$request->input('q', ''));

            if (!$year || !$month) {
                return response()->json([
                    'ok'=>false,
                    'msg'=>'Pick Year & Month.',
                    'rows'=>[],
                ]);
            }

            $start = Carbon::create($year, $month, 1)->startOfDay();
            $end   = $start->copy()->endOfMonth()->endOfDay();
            $daysInMonth = $start->daysInMonth;
            $days = range(1, $daysInMonth);

            // Tables/columns
            $attTable = $this->firstExistingTable(['attendances','attendance','attendence']);
            if (!$attTable) {
                return response()->json(['ok'=>false,'msg'=>'Attendance table not found.','rows'=>[]]);
            }
            foreach (['date','attendable_id','attendable_type'] as $col) {
                if (!Schema::hasColumn($attTable,$col)) {
                    return response()->json(['ok'=>false,'msg'=>'Attendance schema missing required columns.','rows'=>[]]);
                }
            }

            $staffTable = $this->firstExistingTable(['staff','employees','users']);
            if (!$staffTable || !Schema::hasColumn($staffTable,'id')) {
                return response()->json(['ok'=>false,'msg'=>'Staff table not found or invalid.','rows'=>[]]);
            }

            // helpful synonyms
            $stId    = 'id';
            $stName  = $this->firstExistingColumn($staffTable, ['name','full_name','display_name']);
            $stFirst = $this->firstExistingColumn($staffTable, ['first_name','firstname','given_name']);
            $stMid   = $this->firstExistingColumn($staffTable, ['middle_name','middlename']);
            $stLast  = $this->firstExistingColumn($staffTable, ['last_name','lastname','surname','family_name']);
            $stReg   = $this->firstExistingColumn($staffTable, ['reg_no','employee_no','emp_no','staff_no','code','reg_number','register_no']);
            // designation field (ID) in staff table (you said it is "designation")
            $stDesig = $this->firstExistingColumn($staffTable, ['designation','staff_designation_id','designation_id','role_id']);

            // (Optional) Attendance.reg_no column (if present)
            $attRegCol = Schema::hasColumn($attTable,'reg_no') ? 'reg_no' : null;

            // Morph types for Staff
            $staffTypes = $this->staffMorphTypes(); // includes 'App\\Models\\Staff'

            // ===== Get staff IDs having attendance in the month (apply filters here) =====
            $q = DB::table($attTable)
                ->select("{$attTable}.attendable_id as sid")
                ->whereIn("{$attTable}.attendable_type", $staffTypes)
                ->whereDate("{$attTable}.date", '>=', $start->toDateString())
                ->whereDate("{$attTable}.date", '<=', $end->toDateString())
                ->join($staffTable, "{$staffTable}.{$stId}", '=', "{$attTable}.attendable_id");

            // Filter: designation (ID on staff table)
            if ($designationId && $stDesig && Schema::hasColumn($staffTable,$stDesig)) {
                $q->where("{$staffTable}.{$stDesig}", $designationId);
            }

            // Filter: q (reg no / name)
            if ($qStr !== '') {
                $q->where(function($w) use ($qStr, $attTable, $attRegCol, $staffTable, $stReg, $stName, $stFirst, $stMid, $stLast) {
                    if ($attRegCol) $w->orWhere("{$attTable}.{$attRegCol}", 'like', "%{$qStr}%");
                    if ($stReg)     $w->orWhere("{$staffTable}.{$stReg}", 'like', "%{$qStr}%");
                    if ($stName)    $w->orWhere("{$staffTable}.{$stName}", 'like', "%{$qStr}%");
                    // build name from parts if needed
                    if (!$stName && $stFirst) $w->orWhere("{$staffTable}.{$stFirst}", 'like', "%{$qStr}%");
                    if (!$stName && $stMid)   $w->orWhere("{$staffTable}.{$stMid}", 'like', "%{$qStr}%");
                    if (!$stName && $stLast)  $w->orWhere("{$staffTable}.{$stLast}", 'like', "%{$qStr}%");
                });
            }

            $staffIds = $q->distinct()->pluck('sid')->all();

            if (!$staffIds) {
                return response()->json([
                    'ok'   => true,
                    'type' => 'Staff Attendance',
                    'days' => $days,
                    'rows' => [],
                    'meta' => [
                        'total_staff' => 0,
                        'designation_title' => $designationId ? $this->designationTitle($designationId) : 'All',
                    ],
                ]);
            }

            // Staff info (reg_no + display name)
            $info = $this->staffInfo($staffTable, $staffIds, $stReg, $stName, $stFirst, $stMid, $stLast);

            // Attendance map for these staff in the month
            $statusTable = Schema::hasTable('attendance_statuses') ? 'attendance_statuses' : null;
            $statusId    = $statusTable ? ($this->firstExistingColumn($statusTable, ['id'])) : null;
            $statusCode  = $statusTable ? ($this->firstExistingColumn($statusTable, ['code','short_code','symbol','abbr','title','name','status'])) : null;

            $aq = DB::table($attTable)
                ->select("{$attTable}.attendable_id as sid", DB::raw("DATE(`{$attTable}`.`date`) as d"))
                ->whereIn("{$attTable}.attendable_type", $staffTypes)
                ->whereIn("{$attTable}.attendable_id", $staffIds)
                ->whereDate("{$attTable}.date", '>=', $start->toDateString())
                ->whereDate("{$attTable}.date", '<=', $end->toDateString());

            if (Schema::hasColumn($attTable,'attendance_status_id')) {
                if ($statusTable && $statusId && $statusCode) {
                    $aq->leftJoin($statusTable, "{$statusTable}.{$statusId}", '=', "{$attTable}.attendance_status_id")
                       ->addSelect(DB::raw("COALESCE(`{$statusTable}`.`{$statusCode}`, '') as s"));
                } else {
                    $aq->addSelect("{$attTable}.attendance_status_id as s");
                }
            } else {
                // fallback: if no status id column, try a generic "status"
                $statusCol = $this->firstExistingColumn($attTable, ['status','attendance','present']);
                if ($statusCol) $aq->addSelect("{$attTable}.{$statusCol} as s");
                else $aq->addSelect(DB::raw("'' as s"));
            }

            $rowsRaw = $aq->get();
            $map = [];
            foreach ($rowsRaw as $r) {
                $map[$r->sid][$r->d] = $this->normalizeStatus($r->s);
            }

            // Build grid rows
            $rows = [];
            foreach ($staffIds as $sid) {
                $display = $info[$sid] ?? ['reg_no'=>'—','name'=>'—'];
                $row = [
                    'staff_id' => $sid,
                    'reg_no'   => $display['reg_no'],
                    'name'     => $display['name'],
                    'd'        => [],
                    'count'    => ['P'=>0,'A'=>0,'L'=>0,'H'=>0,'LV'=>0,'EL'=>0],
                ];
                foreach ($days as $d) {
                    $key = $start->copy()->day($d)->toDateString();
                    $code = isset($map[$sid][$key]) ? $map[$sid][$key] : '';
                    $row['d'][$d] = $code;
                    if ($code && isset($row['count'][$code])) $row['count'][$code]++;
                }
                $rows[] = $row;
            }

            return response()->json([
                'ok'   => true,
                'type' => 'Staff Attendance',
                'days' => $days,
                'rows' => $rows,
                'meta' => [
                    'total_staff'      => count($staffIds),
                    'designation_title'=> $designationId ? $this->designationTitle($designationId) : 'All',
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('StaffMonthlyReport@data failed', [
                'err'=>$e->getMessage(), 'line'=>$e->getLine(), 'file'=>$e->getFile(),
                'req'=>$request->all(),
            ]);
            return response()->json([
                'ok'=>false,
                'msg'=>'Failed to load Staff Monthly Attendance Report.',
                'rows'=>[],
            ], 200);
        }
    }

    /* ============================
       Helpers
       ============================ */

    private function staffMorphTypes(): array
    {
        $cands = [
            'App\\Models\\Staff',
            'App\\Staff',
            'staff',
            'Staff',
        ];
        if (class_exists('App\\Models\\Staff')) array_unshift($cands, 'App\\Models\\Staff');
        elseif (class_exists('App\\Staff'))     array_unshift($cands, 'App\\Staff');
        return array_values(array_unique($cands));
    }

    private function designationOptions(): array
    {
        // Prefer staff_designations (id + title)
        if (Schema::hasTable('staff_designations') && Schema::hasColumn('staff_designations','id')) {
            $titleCol = $this->firstExistingColumn('staff_designations',['title','name','designation']);
            if ($titleCol) {
                return DB::table('staff_designations')
                    ->when(Schema::hasColumn('staff_designations','status'), fn($q)=>$q->where('status',1))
                    ->orderBy($titleCol)
                    ->get(['id', DB::raw("`{$titleCol}` as title")])
                    ->map(fn($r)=>['id'=>$r->id,'title'=>$r->title])
                    ->all();
            }
        }
        // fallback: distinct IDs from staff.designation (labels = the ID)
        $staffTable = $this->firstExistingTable(['staff','employees','users']);
        $stDesig = $staffTable ? $this->firstExistingColumn($staffTable,['designation','staff_designation_id','designation_id','role_id']) : null;
        if ($staffTable && $stDesig) {
            return DB::table($staffTable)->whereNotNull($stDesig)->where($stDesig,'>',0)
                ->distinct()->orderBy($stDesig)
                ->pluck($stDesig)->map(fn($id)=>['id'=>$id,'title'=> (string)$id ])->all();
        }
        return [];
    }

    private function designationTitle(int $id): ?string
    {
        if (!$id) return null;
        if (Schema::hasTable('staff_designations')) {
            $titleCol = $this->firstExistingColumn('staff_designations',['title','name','designation']);
            $idCol    = $this->firstExistingColumn('staff_designations',['id']);
            if ($titleCol && $idCol) {
                $row = DB::table('staff_designations')->select([$titleCol.' as t'])->where($idCol,$id)->first();
                if ($row && $row->t) return $row->t;
            }
        }
        return null;
    }

    private function staffInfo(string $staffTable, array $ids, ?string $stReg, ?string $stName, ?string $stFirst, ?string $stMid, ?string $stLast): array
    {
        $out = [];
        if (!$ids) return $out;

        $sel = ["{$staffTable}.id"];
        if ($stReg)   $sel[] = "{$staffTable}.{$stReg} as reg_no";
        if ($stName)  $sel[] = "{$staffTable}.{$stName} as st_name";
        if ($stFirst) $sel[] = "{$staffTable}.{$stFirst} as st_first";
        if ($stMid)   $sel[] = "{$staffTable}.{$stMid} as st_mid";
        if ($stLast)  $sel[] = "{$staffTable}.{$stLast} as st_last";

        $rows = DB::table($staffTable)->select($sel)->whereIn("{$staffTable}.id",$ids)->get();
        foreach ($rows as $r) {
            $display = '';
            if ($stName && !empty($r->st_name)) $display = $r->st_name;
            else {
                $parts = [];
                if ($stFirst && !empty($r->st_first)) $parts[] = trim((string)$r->st_first);
                if ($stMid && !empty($r->st_mid))     $parts[] = trim((string)$r->st_mid);
                if ($stLast && !empty($r->st_last))   $parts[] = trim((string)$r->st_last);
                $display = $parts ? implode(' ', $parts) : '—';
            }
            $out[$r->id] = [
                'reg_no' => $stReg ? ($r->reg_no ?? '—') : '—',
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
}
