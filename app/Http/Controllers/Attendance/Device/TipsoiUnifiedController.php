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
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Jobs\AttendanceJobs\SyncLogsRunJob;
use App\Jobs\AttendanceJobs\BatchUpdateRunJob;
use App\Jobs\AttendanceJobs\BatchUpdateChunkJob;
use App\Models\Faculty;
use App\Models\Semester;

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
        $data = ['panel'=>$this->panel,'base_route'=>$this->base_route];
        $data['faculties'] = Faculty::select('id','faculty')->where('status',1)->orderBy('faculty')->get();
        $data['semesters'] = Semester::select('id','semester')->where('status',1)->orderBy('semester')->get();
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

    /**
     * The devices this college runs, and whether each one is talking to us.
     *
     * Read from our own table, not from the manufacturer's cloud. The cloud only knows about
     * devices registered with them; a device pointed at this server is invisible to it, which is
     * why a perfectly healthy reader was showing as inactive on this screen.
     *
     * "Connected" means it has sent a heartbeat recently. Not a live probe of the device: this
     * screen is opened from the office and from the live server, and neither can reach inside the
     * college network to knock on the device's door. The heartbeat is the device telling us, and
     * that reaches us wherever we are.
     */
    public function getAllDevices()
    {
        $manager = app(\App\Services\Attendance\DeviceManager::class);

        /* Anything that has gone quiet stops counting as connected before we report. */
        app(\App\Services\Attendance\DeviceRegistry::class)
            ->markStaleAsOffline((int) config('devices.offline_after_minutes', 5));

        $local = [];
        foreach (\App\Models\TipsoiDevice::orderBy('id')->get() as $d) {
            $up = (bool) $d->connected;

            $local[] = [
                'id'         => $d->id,
                'identifier' => $d->identifier,
                'name'       => $d->name ?: 'Device',
                'vendor'     => $d->vendor,
                'ip'         => $d->ip_address,
                'location'   => $d->location,
                'status'     => $up ? 'active' : 'inactive',
                'connected'  => $up ? 1 : 0,
                'last_seen'  => $d->last_seen ? $d->last_seen->diffForHumans() : 'never',
                'enrolling'  => (bool) $d->enrol_students,
                'source'     => 'local',
            ];
        }

        /*
         * The vendor cloud as well, but only when it has been given a token. Asking without one
         * returns an empty list that looks exactly like "no devices" - which is how this screen
         * came to report nothing at all for weeks.
         */
        $list = [];
        if (trim((string) config('tipsoi.cs.api_token')) !== '') {
            try {
                $cloud = $this->api->devices();
                if (is_array($cloud)) { $list = $cloud; }
            } catch (\Throwable $e) {
                Log::warning('Vendor cloud device list failed', ['error' => $e->getMessage()]);
            }
        }

        if ($local) {
            return response()->json(['success' => true, 'data' => array_merge($local, $list)]);
        }

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

    /* ---------------- Managing devices from the application ---------------- */

    protected function devices()
    {
        return app(\App\Services\Attendance\DeviceManager::class);
    }

    /**
     * Add a device, or update one already known by that serial.
     *
     * The serial is the device's own deviceKey - the value it puts in every heartbeat. It is not
     * ours to invent: type it wrong and the device's reports will create a second row rather than
     * updating this one. It is printed on the device and shown in its Device Information screen.
     */
    public function storeDevice(Request $request)
    {
        $data = $request->validate([
            'identifier'     => 'required|string|max:191',
            'name'           => 'nullable|string|max:191',
            'vendor'         => 'nullable|string|max:40',
            'ip_address'     => 'nullable|string|max:64',
            'location'       => 'nullable|string|max:191',
            'notes'          => 'nullable|string|max:255',
            'enrol_students' => 'nullable|boolean',
        ]);

        try {
            $device = $this->devices()->add($data);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'data' => $device]);
    }

    /**
     * Write our callback addresses into the device, so it starts reporting here.
     *
     * Reaches out to the device over the local network, which is the one thing in this whole
     * arrangement that does not work from the live server: the college router does not let the
     * internet in. Run this from a computer at the college. It is a one-time job - afterwards the
     * device calls us and nobody needs to be near it again.
     */
    public function connectDevice(Request $request)
    {
        $data = $request->validate([
            'identifier' => 'required|string|max:191',
            'base_url'   => 'nullable|string|max:191',
            'password'   => 'nullable|string|max:191',
        ]);

        $device = \App\Models\TipsoiDevice::where('identifier', $data['identifier'])->first();
        if (!$device) {
            return response()->json(['success' => false, 'message' => 'No device with that serial.'], 404);
        }

        try {
            $results = $this->devices()->connect(
                $device,
                $data['base_url'] ?? null,
                $data['password'] ?? null
            );
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $accepted = count(array_filter($results, function ($r) { return $r['accepted']; }));

        return response()->json([
            'success' => $accepted === count($results),
            'message' => $accepted . ' of ' . count($results) . ' addresses accepted',
            'data'    => $results,
        ]);
    }

    /**
     * What the device is currently pointed at. Read this before connecting - it is the only
     * record of where its punches were going, and connecting overwrites it.
     */
    public function readDeviceCallbacks(Request $request)
    {
        $device = \App\Models\TipsoiDevice::where('identifier', $request->get('identifier'))->first();
        if (!$device) {
            return response()->json(['success' => false, 'message' => 'No device with that serial.'], 404);
        }

        $current = $this->devices()->readCallbacks($device, $request->get('password'));

        return response()->json([
            'success' => $current !== null,
            'message' => $current === null ? 'The device could not be reached from here.' : 'ok',
            'data'    => $current,
        ]);
    }

    /**
     * Take a device out of service. Not a delete - its punches are somebody's attendance.
     */
    public function retireDevice(Request $request)
    {
        $device = $this->devices()->retire($request->get('identifier'));

        return response()->json([
            'success' => (bool) $device,
            'message' => $device ? 'Taken out of service.' : 'No device with that serial.',
        ]);
    }

    /* ---------------- Enrolling students onto the device ---------------- */

    /**
     * Put students in the queue the device collects from.
     *
     * Nothing is sent to the device here, and nothing can be: it sits behind the college wifi
     * where this server cannot reach it. Rows go into device_tasks and the device asks for them,
     * one at a time, of its own accord. So this returns as soon as the queue is written and the
     * device catches up over the following minutes.
     *
     * Three modes, and the order matters. Check reports without writing - it is the only way to
     * find out how many photos the device will refuse before it refuses them one by one. Twenty
     * is a rehearsal. Everybody is everybody.
     */
    public function enrolStudents(Request $request)
    {
        $mode = $request->input('mode', 'check');

        $args = ['--check' => true];
        if ($mode === 'small') { $args = ['--limit' => 20]; }
        if ($mode === 'all')   { $args = []; }

        if (!in_array($mode, ['check', 'small', 'all'], true)) {
            return response()->json(['success' => false, 'message' => 'Unknown mode.'], 422);
        }

        /* A thousand students is a few thousand rows and a lot of photo checks. */
        @set_time_limit(900);

        try {
            \Artisan::call('attendance:enrol-students', $args);
            $output = \Artisan::output();
        } catch (\Throwable $e) {
            Log::error('Enrolment run failed', ['mode' => $mode, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Enrolment failed: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json(['success' => true, 'output' => $output]);
    }

    /**
     * How much of the queue is left, and anything the device refused.
     *
     * The refusals matter more than the numbers: a task the device rejected names the student and
     * says why, and that is where a photo the camera cannot use shows up.
     */
    public function enrolmentStatus()
    {
        $counts = [];
        foreach (['queued', 'sent', 'done', 'failed'] as $s) {
            $counts[$s] = \App\Models\DeviceTask::where('status', $s)->count();
        }

        $out = "the queue\n";
        foreach ($counts as $k => $v) { $out .= sprintf("  %-8s %d\n", $k, $v); }

        $out .= "\ndevices\n";
        foreach (\App\Models\TipsoiDevice::orderBy('id')->get() as $d) {
            $out .= sprintf("  %-24s connected=%s  last seen %s\n",
                $d->identifier, $d->connected ? 'yes' : 'no',
                $d->last_seen ? $d->last_seen->diffForHumans() : 'never');
        }

        $failed = \App\Models\DeviceTask::where('status', 'failed')->orderBy('id', 'desc')->limit(15)->get();
        if ($failed->count()) {
            $out .= "\nthe device refused these\n";
            foreach ($failed as $f) {
                $p = is_array($f->payload) ? $f->payload : [];
                $who = $p['id'] ?? $p['personId'] ?? '?';
                $out .= sprintf("  student %-8s %-18s %s\n", $who, $f->action,
                    mb_substr((string) $f->last_error, 0, 140));
            }
        }

        if ($counts['queued'] > 0) {
            $out .= "\n" . $counts['queued'] . " still waiting. The device takes one at a time and asks\n";
            $out .= "for the next straight away, so this should fall steadily. Press again to look.\n";
        }

        return response()->json(['success' => true, 'output' => $out]);
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
            $statusCode = AttendanceStatus::whereKey($statusId)->value('code');
            $att = Attendance::create([
                'date'                 => $date,
                'attendable_type'      => $attType,
                'attendable_id'        => $model->id,
                'reg_no'               => $reg,
                'attendance_status_id' => $statusId,
                'check_in_at'          => $lt,
                'check_out_at'         => $lt,
                'source'               => 'device',
                'notification_status'  => 'pending',
                'meta'                 => ['notify' => ['last_status' => $statusCode, 'queued_at' => now()->toDateTimeString()]],
            ]);

            /* instant SMS: dispatch right away instead of waiting for the 5-min sweep */
            if ($type === 'student') {
                try {
                    \App\Jobs\AttendanceJobs\SendAttendanceNotification::dispatch($att->id)->onQueue('notifications');
                } catch (\Throwable $e) {
                    Log::warning('Instant attendance notification dispatch failed', ['id' => $att->id, 'err' => $e->getMessage()]);
                }
            }
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
            $facultyId  = (int) $req->input('faculty_id', 0);
            $semesterId = (int) $req->input('semester_id', 0);

            // Validate minimally (we only enqueue)
            if (!in_array($who, ['student','staff','both'], true)) {
                return response()->json(['message'=>'Invalid who'], 422);
            }
            if (!is_array($devs) || !count($devs)) {
                return response()->json(['message'=>'At least one device required'], 422);
            }

            /*
             * Chunked dispatch: many small jobs instead of one monster job.
             * The old single BatchUpdateRunJob died on shared hosting
             * (queue retry_after / worker timeout << full run duration).
             */
            $chunkSize = 60;
            $chunks = []; // list of [type, ids[]]

            if (in_array($who, ['student','both'], true)) {
                $q = Student::query()->select('id');
                if (Schema::hasColumn('students','reg_no')) {
                    $q->whereNotNull('reg_no')->where('reg_no','!=','');
                }
                if ($facultyId > 0 && Schema::hasColumn('students','faculty'))   $q->where('faculty', $facultyId);
                if ($semesterId > 0 && Schema::hasColumn('students','semester')) $q->where('semester', $semesterId);
                foreach (array_chunk($q->orderBy('id')->pluck('id')->all(), $chunkSize) as $ids) {
                    $chunks[] = ['student', $ids];
                }
            }
            if (in_array($who, ['staff','both'], true)) {
                $q = Staff::query()->select('id');
                if (Schema::hasColumn('staff','reg_no')) {
                    $q->whereNotNull('reg_no')->where('reg_no','!=','');
                }
                foreach (array_chunk($q->orderBy('id')->pluck('id')->all(), $chunkSize) as $ids) {
                    $chunks[] = ['staff', $ids];
                }
            }

            $totalPeople = array_sum(array_map(function ($c) { return count($c[1]); }, $chunks));
            if ($totalPeople === 0) {
                $run->update(['status'=>'failed','error'=>'No people matched the selected filters.','finished_at'=>now()]);
                return response()->json(['message'=>'No people matched the selected filters.'], 422);
            }

            $run->update(['total_steps' => $totalPeople]);

            $opts = ['devices'=>$devs, 'with_photos'=>$photos, 'use_rfid'=>$rfid];
            $totalChunks = count($chunks);
            foreach ($chunks as $i => [$type, $ids]) {
                BatchUpdateChunkJob::dispatch($run->id, $type, $ids, $i + 1, $totalChunks, $opts);
            }
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
