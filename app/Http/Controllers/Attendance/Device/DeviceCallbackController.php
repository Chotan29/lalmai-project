<?php

namespace App\Http\Controllers\Attendance\Device;

use App\Http\Controllers\Controller;
use App\Models\DeviceTask;
use App\Models\TipsoiDevice;
use App\Services\Attendance\DeviceRegistry;
use App\Services\Attendance\PunchIngestor;
use App\Services\Devices\DeviceDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * The four things any attendance device does to us, in one place.
 *
 * The device always calls; we never call it. It rings once a minute to ask whether there is work,
 * collects the work, reports back, and posts a record whenever it recognises a face. Those four
 * exchanges are the same whoever built the hardware - only the wording differs, and the wording
 * is the driver's problem.
 *
 * A brand gets its own subclass, which does nothing but name its driver. Everything below is
 * shared, so a second manufacturer costs a handful of lines rather than a second copy of all
 * this.
 *
 * Lives under Attendance/Device rather than beside the callback controllers in API/, and the
 * reason is worth writing down. That folder is spelled API while the namespace in every file
 * inside it says Api. Windows does not care; the live server is Linux and does, so psr-4 looks
 * for app/Http/Controllers/Api/ and finds nothing. The existing controllers there survive only
 * because composer's optimised classmap was built when they already existed and records their
 * real path - a file added afterwards has no such entry and cannot be loaded at all. This one
 * sits in a folder whose spelling matches its namespace, so it loads without anyone having to
 * run composer on the server.
 */
abstract class DeviceCallbackController extends Controller
{
    /** @return DeviceDriver */
    abstract protected function driver();

    protected function registry()
    {
        return app(DeviceRegistry::class);
    }

    /**
     * Everything the device sends, written down exactly as it arrived.
     *
     * Manufacturers document their own interfaces well and what they send you barely at all. When
     * a field turns out to mean something other than we assumed, this file is the evidence - and
     * it is the only copy of a punch that failed to parse.
     */
    protected function record($kind, Request $r, $note = null)
    {
        $line = json_encode([
            'at'     => now()->toDateTimeString(),
            'vendor' => $this->driver()->vendor(),
            'kind'   => $kind,
            'note'   => $note,
            'ip'     => $r->ip(),
            'input'  => $r->all(),
            'raw'    => mb_substr((string) $r->getContent(), 0, 4000),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        @file_put_contents(storage_path('logs/device-callback.log'), $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    /**
     * Work queued for this device.
     *
     * Matched on the serial the device gives and on the address it reports for itself - never on
     * the address the request came from. Over the internet that is the college router, the same
     * for every device behind it, so matching on it would hand one device another's work.
     */
    protected function pendingTasks(TipsoiDevice $device = null, Request $r = null)
    {
        $keys = array_values(array_filter(array_unique([
            $device ? $device->ip_address : null,
            $device ? $device->identifier : null,
        ])));

        if (!$keys) { return DeviceTask::whereRaw('1 = 0'); }

        return DeviceTask::whereIn('device_ip', $keys)->where('status', 'queued')->orderBy('id');
    }

    protected function deviceFor(Request $r)
    {
        $key = trim((string) $r->input('deviceKey', $r->input('devicekey', '')));
        return $key !== '' ? TipsoiDevice::where('identifier', $key)->first() : null;
    }

    /* ---------------- the four exchanges ---------------- */

    /**
     * Once a minute. Answer quickly: the device is holding the line and cannot read a face while
     * it waits.
     */
    public function heartbeat(Request $r)
    {
        $driver = $this->driver();

        /* Some devices post recognitions to the heartbeat address as well as their own. Let the
           driver decide, rather than trusting the url. */
        $punch = $driver->parsePunch($r);
        if ($punch !== null) {
            return $this->recognition($r);
        }

        $this->record('heartbeat', $r);

        $device  = $this->registry()->touch($driver->parseHeartbeat($r), $r->ip());
        $hasWork = $this->pendingTasks($device, $r)->exists();

        return response()->json($driver->heartbeatReply($hasWork));
    }

    /**
     * A face was recognised.
     */
    public function recognition(Request $r)
    {
        $driver = $this->driver();
        $punch  = $driver->parsePunch($r);

        if ($punch === null) {
            $this->record('recognition', $r, 'could not be parsed');
            return response()->json(['result' => true]);
        }

        try {
            list($stored, $why) = app(PunchIngestor::class)->ingest($punch);
            $this->record('recognition', $r, $why);
        } catch (\Throwable $e) {
            /* Answer the device anyway. A punch we failed to store is still in the device's own
               memory and can be collected later; a device left waiting on an error page is a
               queue of students at the gate. */
            $this->record('recognition', $r, 'failed: ' . $e->getMessage());
            Log::error('Punch could not be stored', ['error' => $e->getMessage()]);
        }

        return response()->json(['result' => true]);
    }

    /**
     * The device has been told there is work and has come for it. One task at a time.
     */
    public function getTask(Request $r)
    {
        $driver = $this->driver();
        $device = $this->deviceFor($r);
        $task   = $this->pendingTasks($device, $r)->first();

        if (!$task) {
            $this->record('get-task', $r, 'queue empty');
            return response()->json($driver->heartbeatReply(false));
        }

        $task->status  = 'sent';
        $task->sent_at = now();
        $task->save();

        $this->record('get-task', $r, 'sent task ' . $task->id . ' (' . $task->action . ')');

        return response()->json($driver->formatTask($task));
    }

    /**
     * The device reports how it went, and asks whether there is more.
     *
     * Saying yes matters: the device fetches the next task immediately instead of waiting for the
     * next heartbeat. Enrolling a thousand students one a minute would take seventeen hours.
     */
    public function taskResult(Request $r)
    {
        $driver = $this->driver();
        $device = $this->deviceFor($r);

        $taskNo = (string) $r->input('taskNo', $r->input('id', ''));
        $id     = (int) preg_replace('/\D/', '', $taskNo);
        $task   = $id > 0 ? DeviceTask::find($id) : null;

        /* How a device says "that worked" is its own business - Tipsoi sends back its whole api
           response as a json string - so the driver decides. */
        $ok = $driver->taskSucceeded($r);

        if ($task) {
            $task->status = $ok ? 'done' : 'failed';
            /* Keep what the device actually said, not a phrase of ours. When a task does fail,
               the reason is in there and nowhere else. */
            $task->last_error = $ok ? null : mb_substr(
                (string) ($r->input('message') ?: json_encode($r->input('result'))), 0, 500
            );
            $task->done_at    = now();
            $task->save();
        }

        $this->record('task-result', $r, $task ? ('task ' . $task->id . ' ' . $task->status) : 'unknown task ' . $taskNo);

        $more = $this->pendingTasks($device, $r)->exists();

        return response()->json($driver->taskResultReply($more));
    }

    /**
     * Photo registration callback. Recorded only - nothing downstream needs it yet.
     */
    public function imgReg(Request $r)
    {
        $this->record('img-reg', $r);
        return response()->json(['result' => true]);
    }

    protected function truthy($v)
    {
        if (is_bool($v)) { return $v; }
        return in_array(strtolower(trim((string) $v)), ['1', 'true', 'yes', 'y'], true);
    }
}
