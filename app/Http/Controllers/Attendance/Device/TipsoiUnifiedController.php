<?php
// php artisan queue:work --queue=attendance --tries=1
namespace App\Http\Controllers\Attendance\Device;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Services\InovaceApi;
use App\Models\BiometricPerson;
use App\Models\IntegrationCursor;
use App\Models\IntegrationRun;
use App\Models\Student;
use App\Models\Staff;
use App\Models\StudentBatch;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Jobs\AttendanceJobs\SyncLogsRunJob;
use App\Jobs\AttendanceJobs\BatchUpdateRunJob;

class TipsoiUnifiedController extends CollegeBaseController
{
    protected InovaceApi $api;
    protected string $view_path = 'attendance.device';
    protected string $base_route = 'attendance.tipsoi';
    protected string $panel = 'Tipsoi Biometric Devices';

    public function __construct(InovaceApi $api)
    {
        $this->api = $api;
    }

    public function dashboard()
    {
        $data = [
            'panel'=>$this->panel,
            'base_route'=>$this->base_route,
            'studentBatches'=>StudentBatch::orderBy('title')->get(['id','title']),
        ];
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    /* ---------------- Utils ---------------- */

    protected function isActive($type, $model): bool
    {
        $table = $type === 'student' ? 'students' : 'staff';
        if (Schema::hasColumn($table, 'status')) {
            $v = $model->status;
            if (is_numeric($v)) return ((int)$v) === 1;
            return strtolower((string)$v) === 'active';
        }
        return true;
    }

    protected function personIdentifierFor($type, $model): string
    {
        if ($type === 'student') {
            if (Schema::hasColumn('students','reg_no') && !empty($model->reg_no)) return (string) $model->reg_no;
            return 'STU-'.$model->id;
        }
        if (Schema::hasColumn('staff','reg_no') && !empty($model->reg_no)) return (string) $model->reg_no;
        return 'STF-'.$model->id;
    }

    protected function baseNameFor($type, $model): string
    {
        if ($type === 'student') {
            $name = trim(($model->first_name ?? '') . ' ' . (($model->middle_name ?? '') ? $model->middle_name.' ' : '') . ($model->last_name ?? ''));
            return $name ?: ('Student#'.$model->id);
        }
        $name = 'Staff#'.$model->id;
        if (Schema::hasColumn('staff','first_name')) {
            $name = trim(($model->first_name ?? '') . ' ' . (($model->middle_name ?? '') ? $model->middle_name.' ' : '') . ($model->last_name ?? ''));
        } elseif (Schema::hasColumn('staff','name') && $model->name) {
            $name = $model->name;
        }
        return $name ?: ('Staff#'.$model->id);
    }

    protected function ensureStatusId($code = 'P'): int
    {
        $id = AttendanceStatus::where('code', strtoupper($code))->value('id');
        if ($id) return (int) $id;
        $row = AttendanceStatus::firstOrCreate(['code'=>'P'], ['label'=>'Present','order'=>1,'color'=>'#10b981']);
        return (int) $row->id;
    }

    /* ---------------- Devices ---------------- */

    public function getAllDevices()
    {
        $list = $this->api->devices();
        // array:1 [
        //   0 => array:24 [
        //     "id" => 48108
        //     "identifier" => "52018"
        //     "device_category_id" => 6
        //     "vendor_id" => "E03C1CB53A1E5601"
        //     "server_url" => null
        //     "firmware_version" => null
        //     "phone_number" => "123456"
        //     "sim_id" => null
        //     "description" => ""
        //     "location" => "Dhaka, Bangladesh"
        //     "imei_number" => null
        //     "timezone_offset_minutes" => 360
        //     "type" => "both"
        //     "server_id" => 1
        //     "has_enrollment_feature" => 0
        //     "is_mqtt_enabled" => 0
        //     "mqtt_allow_batch_rfid" => 0
        //     "connected" => 0
        //     "data_dump_requested" => 0
        //     "last_communication_at" => "2025-09-04 19:42:43"
        //     "device_type_id" => 3
        //     "total_allocated" => 0
        //     "last_seen" => "19 seconds ago"
        //     "status" => "active"
        //   ]
        // ]
        return response()->json(['success'=>true,'data'=>$list]);
    }

    /* ---------------- Search (active only — optional API if you need it) ---------------- */

    public function searchPeople(Request $req)
    {
        $req->validate([
            'type' => 'required|in:student,staff',
            'q'    => 'nullable|string',
            'per'  => 'nullable|integer|min:1|max:100',
        ]);
        $type = $req->input('type');
        $q    = trim((string) $req->input('q', ''));
        $per  = (int) ($req->input('per', 20) ?: 20);

        if ($type === 'student') {
            $qb = Student::query()->where('status', 1);
            if ($q !== '') {
                $qb->where(function($w) use ($q) {
                    if (Schema::hasColumn('students','reg_no')) $w->orWhere('reg_no', 'like', "%{$q}%");
                    foreach (['first_name','middle_name','last_name'] as $c) {
                        if (Schema::hasColumn('students',$c)) $w->orWhere($c, 'like', "%{$q}%");
                    }
                });
            }
            $rows = $qb->orderBy('id')->limit($per)->get();
            $data = $rows->map(function($m){
                $name = trim(($m->first_name ?? '') . ' ' . (($m->middle_name ?? '') ? $m->middle_name.' ' : '') . ($m->last_name ?? ''));
                if ($name === '') $name = 'Student#'.$m->id;
                $code = Schema::hasColumn('students','reg_no') ? ($m->reg_no ?: null) : null;
                return [
                    'id'   => $m->id,
                    'name' => $name,
                    'code' => $code,
                    'img'  => (Schema::hasColumn('students','student_image') && $m->student_image) ? asset('images/studentProfile/'.$m->student_image) : null,
                ];
            });
            return response()->json(['success'=>true,'data'=>$data]);
        }

        $qb = Staff::query()->where('status', 1);
        if ($q !== '') {
            $qb->where(function($w) use ($q) {
                if (Schema::hasColumn('staff','reg_no')) $w->orWhere('reg_no', 'like', "%{$q}%");
                foreach (['name','first_name','middle_name','last_name'] as $c) {
                    if (Schema::hasColumn('staff',$c)) $w->orWhere($c, 'like', "%{$q}%");
                }
            });
        }
        $rows = $qb->orderBy('id')->limit($per)->get();
        $data = $rows->map(function($m){
            $name = 'Staff#'.$m->id;
            if (Schema::hasColumn('staff','first_name')) {
                $name = trim(($m->first_name ?? '') . ' ' . (($m->middle_name ?? '') ? $m->middle_name.' ' : '') . ($m->last_name ?? ''));
            } elseif (Schema::hasColumn('staff','name') && $m->name) {
                $name = $m->name;
            }
            $code = Schema::hasColumn('staff','reg_no') ? ($m->reg_no ?: null) : null;
            return [
                'id'   => $m->id,
                'name' => $name,
                'code' => $code,
                'img'  => (Schema::hasColumn('staff','staff_image') && $m->staff_image) ? asset('images/staff/'.$m->staff_image) : null,
            ];
        });
        return response()->json(['success'=>true,'data'=>$data]);
    }

    /* ---------------- Push + allocate (kept) ---------------- */

    public function pushPersonToDevice(Request $req)
    {
        $req->validate([
            'type'              => 'required|in:student,staff',
            'id'                => 'required|integer|min:1',
            'rfid'              => 'nullable|string|max:64',
            'device_identifier' => 'nullable', // string|array
            'allocate'          => 'nullable',
        ]);

        $type = $req->input('type');
        $id   = (int) $req->input('id');

        $model = $type === 'student' ? Student::find($id) : Staff::find($id);
        if (!$model) return response()->json(['success'=>false,'message'=>'Person not found.'], 404);
        if (!$this->isActive($type, $model)) return response()->json(['success'=>false,'message'=>'Inactive profile. Please contact administration.'], 422);

        $identifier = $this->personIdentifierFor($type, $model);
        $name       = $this->baseNameFor($type, $model);

        $first = trim((string) ($model->first_name ?? ($model->name ?? '')));
        $last  = trim((string) ($model->last_name ?? ''));
        $pdt   = mb_substr($first !== '' ? $first : 'Welcome', 0, 20);
        $sdt   = mb_substr($last  !== '' ? $last  : '-',       0, 20);

        $rfidIn = trim((string) $req->input('rfid', ''));
        $rfid   = $rfidIn !== '' ? $rfidIn : (property_exists($model,'reg_no') ? (string) ($model->reg_no ?? '') : '');

        $imagePath = null;
        if ($type === 'student' && Schema::hasColumn('students','student_image') && !empty($model->student_image)) {
            $imagePath = public_path('images/studentProfile/' . $model->student_image);
        } elseif ($type === 'staff' && Schema::hasColumn('staff','staff_image') && !empty($model->staff_image)) {
            $imagePath = public_path('images/staff/' . $model->staff_image);
        }

        $payload = [
            'identifier'             => $identifier,
            'name'                   => $name,
            'person_type'            => 'employee',
            'primary_display_text'   => $pdt,
            'secondary_display_text' => $sdt,
        ];
        if ($rfid !== '') $payload['rfid'] = $rfid;

        $res = $this->api->upsertPersonSafe($payload, $imagePath);
        if (!($res['ok'] ?? false)) {
            return response()->json(['success'=>false,'message'=>$res['message'] ?? 'Push failed'], 422);
        }

        $map = BiometricPerson::firstOrNew([
            'attendable_type' => $type === 'student' ? Student::class : Staff::class,
            'attendable_id'   => $model->id,
        ]);
        $map->person_identifier      = $identifier;
        if ($rfid !== '') $map->rfid = $rfid;
        $map->primary_display_text   = $pdt;
        $map->secondary_display_text = $sdt;
        $map->last_pushed_at         = Carbon::now();
        $map->save();

        $allocated = [];
        $ok = 0; $total = 0;
        $allocate = filter_var($req->input('allocate', false), FILTER_VALIDATE_BOOLEAN);

        if ($allocate && $req->filled('device_identifier')) {
            $devices = is_array($req->device_identifier) ? $req->device_identifier : [$req->device_identifier];
            $total = count($devices);
            foreach ($devices as $dev) {
                $dev = is_array($dev) ? ($dev['value'] ?? '') : $dev;
                $dev = (string) $dev;

                $r = $this->api->allocatePersonToDevice($dev, $identifier, 'allocate');
                $allocated[$dev] = $r;

                if (is_array($r) && (
                    ($r['success'] ?? false) ||
                    (isset($r['status']) && in_array(strtolower($r['status']), ['pending_sync','queued','ok','success'], true)) ||
                    (isset($r['code']) && (int)$r['code'] === 200)
                )) {
                    $ok++;
                }
            }
        }

        return response()->json([
            'success'      => true,
            'person'       => [
                'identifier' => $identifier,
                'name'       => $name,
                'rfid'       => $rfid,
                'pdt'        => $pdt,
                'sdt'        => $sdt,
            ],
            'allocated'    => $allocated,
            'alloc_stats'  => ['ok'=>$ok, 'total'=>$total],
            'rfid_removed' => (bool) ($res['rfid_removed'] ?? false),
        ]);
    }

    /* ---------------- Manual batch allocate/revoke (kept) ---------------- */

    public function batchAllocations(Request $req)
    {
        $req->validate([
            'action'              => 'required|in:allocate,revoke',
            'device_ids'          => 'nullable|array',
            'device_identifiers'  => 'nullable|array',
            'person_identifiers'  => 'required|array|min:1',
        ]);

        $devices = $req->input('device_ids', $req->input('device_identifiers', []));
        $res     = $this->api->batchAllocations($req->action, $req->person_identifiers, $devices);

        return response()->json(['success'=>true, 'result'=>$res]);
    }

    /* ---------------- Logs sync (legacy direct) — not used by UI now ---------------- */

    public function storeAttendanceLogs(Request $req)
    {
        // still kept for compatibility with your old script; new UI uses queued run
        $perPage = (int) config('inovace.per_page', 500);
        $start = $req->input('start');
        $end   = $req->input('end');

        $cursorKey = config('inovace.cursor_key_logs');
        $cursor = IntegrationCursor::firstOrCreate(['key'=>$cursorKey], ['value'=>null]);

        if (!$start) $start = $cursor->value ?: Carbon::yesterday()->startOfDay()->toDateTimeString();
        if (!$end)   $end   = Carbon::now()->toDateTimeString();

        $page = 1; $total = 0; $maxSync = null;

        do {
            $resp = $this->api->logs($start, $end, $page, $perPage);
            $rows = [];

            if (is_array($resp)) {
                if (isset($resp['data']['data']) && is_array($resp['data']['data'])) $rows = $resp['data']['data'];
                elseif (isset($resp['data']) && is_array($resp['data'])) $rows = $resp['data'];
                elseif (isset($resp['logs']) && is_array($resp['logs'])) $rows = $resp['logs'];
                elseif (isset($resp['items']) && is_array($resp['items'])) $rows = $resp['items'];
                elseif (isset($resp['records']) && is_array($resp['records'])) $rows = $resp['records'];
                else {
                    $i=0; $ok=true; foreach ($resp as $k=>$v){ if($k!==$i){$ok=false;break;} if(!is_array($v)){$ok=false;break;} $i++; }
                    if ($ok && $i>0) $rows = $resp;
                }
            }

            foreach ($rows as $log) {
                $this->applyLogRow($log, $maxSync);
                $total++;
            }

            if (isset($resp['meta']['current_page'], $resp['meta']['last_page'])) {
                $cp = (int) $resp['meta']['current_page'];
                $lp = (int) $resp['meta']['last_page'];
                if ($cp >= $lp) break;
                $page = $cp + 1;
            } else {
                break;
            }
        } while (true);

        if ($maxSync) { $cursor->value = $maxSync; $cursor->save(); }

        return response()->json(['success'=>true,'synced'=>$total,'cursor'=>$cursor->value,'start'=>$start,'end'=>$end]);
    }

    protected function applyLogRow(array $log, &$maxSync)
    {
        $syncTime   = isset($log['sync_time'])   ? $log['sync_time']   : null;
        $loggedTime = isset($log['logged_time']) ? $log['logged_time'] : null;
        $identifier = isset($log['person_identifier']) ? $log['person_identifier'] : null;

        if ($syncTime && (!$maxSync || $syncTime > $maxSync)) $maxSync = $syncTime;
        if (!$loggedTime || !$identifier) return;

        $model = null; $type = null;
        if (Schema::hasColumn('students','reg_no')) {
            $s = Student::where('reg_no', $identifier)->first();
            if ($s) { $model = $s; $type = 'student'; }
        }
        if (!$model && Schema::hasColumn('staff','reg_no')) {
            $s = Staff::where('reg_no', $identifier)->first();
            if ($s) { $model = $s; $type = 'staff'; }
        }
        if (!$model) return;

        $date = Carbon::parse($loggedTime)->startOfDay()->toDateString();
        $attType = $type === 'student' ? Student::class : Staff::class;

        $row = Attendance::whereDate('date', $date)
            ->where('attendable_type', $attType)
            ->where('attendable_id', $model->id)
            ->first();

        $statusId = $this->ensureStatusId('P');
        $lt = Carbon::parse($loggedTime);

        if (!$row) {
            $reg = $this->personIdentifierFor($type, $model);
            Attendance::create([
                'date'                 => $date,
                'attendable_type'      => $attType,
                'attendable_id'        => $model->id,
                'reg_no'               => $reg,
                'attendance_status_id' => $statusId,
                'check_in_at'          => $lt,
                'check_out_at'         => $lt,
                'source'               => 'device',
            ]);
            return;
        }

        if (!$row->check_in_at || $lt->lt($row->check_in_at))   $row->check_in_at = $lt;
        if (!$row->check_out_at || $lt->gt($row->check_out_at)) $row->check_out_at = $lt;
        if (!$row->attendance_status_id) $row->attendance_status_id = $statusId;
        if (!$row->source) $row->source = 'device';
        $row->save();
    }

    public function syncStatus()
    {
        $cursorKey = config('inovace.cursor_key_logs');
        $cur = IntegrationCursor::where('key', $cursorKey)->value('value');
        return response()->json(['success'=>true,'cursor'=>$cur,'now'=>Carbon::now()->toDateTimeString()]);
    }

    public function setHeartbeat()
    {
        return response()->json(['ok'=>true,'time'=>Carbon::now()->toDateTimeString()]);
    }

    /* ---------------- RUNS: queue jobs + poll ---------------- */

    public function storeRun(Request $req)
    {
        // run type can arrive as "type" or "run_type"
        $runType = $req->input('type', $req->input('run_type', ''));
        if (!in_array($runType, ['batch_update','sync_logs'], true)) {
            return response()->json(['message'=>'Invalid run type'], 422);
        }

        $payload = $req->all();

        $run = IntegrationRun::create([
            'type'   => $runType,
            'status' => 'queued',
            'total_steps' => 0,
            'done_steps'  => 0,
            'payload' => $payload,
            'result'  => $runType === 'sync_logs'
                ? ['synced'=>0,'pages'=>0,'errors'=>[]]
                : ['upserted'=>[],'revoked'=>0,'errors'=>[],'allocated'=>['ok'=>0,'total'=>0]],
        ]);

        if ($runType === 'sync_logs') {
            $start = $req->input('start') ?: null;
            $end   = $req->input('end') ?: null;
            SyncLogsRunJob::dispatch($run->id, $start, $end)->onQueue('attendance');
        } else { // batch_update
            // Accept multiple naming variants from the view
            $who    = $req->input('who', $req->input('subject', $req->input('subject_type', $req->input('type_who', 'student'))));
            $devs   = $req->input('device_identifier', []);
            $photos = (int) $req->input('with_photos', 0) === 1;
            $rfid   = (int) $req->input('use_rfid', 0) === 1;

            // Validate minimally (we only enqueue)
            if (!in_array($who, ['student','staff','both'], true)) {
                return response()->json(['message'=>'Invalid who'], 422);
            }
            if (!is_array($devs) || !count($devs)) {
                return response()->json(['message'=>'At least one device required'], 422);
            }

            // Your existing job class – make sure it does NOT declare a $queue property.
            BatchUpdateRunJob::dispatch($run->id, $who, $devs, $photos, $rfid)->onQueue('attendance');
        }

        return response()->json($run->fresh()->toArray());
    }

    public function showRun(IntegrationRun $run)
    {
        return response()->json($run->fresh()->toArray());
    }

    /* ---------------- Debug: peek raw logs page ---------------- */

    public function testLogsSnapshot(Request $req)
    {
        $start = $req->input('start') ?: now()->subDay()->startOfDay()->toDateTimeString();
        $end   = $req->input('end')   ?: now()->toDateTimeString();
        $page  = (int) ($req->input('page') ?: 1);
        $per   = (int) ($req->input('per') ?: 50);

        $resp = $this->api->logs($start, $end, $page, $per);

        $rows = [];
        if (is_array($resp)) {
            if (isset($resp['data']['data']) && is_array($resp['data']['data'])) $rows = $resp['data']['data'];
            elseif (isset($resp['data']) && is_array($resp['data'])) $rows = $resp['data'];
            elseif (isset($resp['logs']) && is_array($resp['logs'])) $rows = $resp['logs'];
            elseif (isset($resp['items']) && is_array($resp['items'])) $rows = $resp['items'];
            elseif (isset($resp['records']) && is_array($resp['records'])) $rows = $resp['records'];
            else {
                $i=0; $ok=true; foreach ($resp as $k=>$v){ if($k!==$i){$ok=false;break;} if(!is_array($v)){$ok=false;break;} $i++; }
                if ($ok && $i>0) $rows = $resp;
            }
        }

        return response()->json([
            'query' => compact('start','end','page','per'),
            'top_level_keys' => is_array($resp) ? array_slice(array_keys($resp), 0, 20) : null,
            'meta' => $resp['meta'] ?? ($resp['data']['meta'] ?? null),
            'count_rows_detected' => is_array($rows) ? count($rows) : 0,
            'first_row' => $rows[0] ?? null,
            'sample' => array_slice($rows ?? [], 0, 3),
            'raw' => $resp,
        ]);
    }
}
