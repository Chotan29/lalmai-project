<?php

namespace App\Services\Tipsoi;

use App\Models\DeviceTask;
use App\Services\Devices\DeviceDriver;
use App\Services\Devices\PunchData;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Tipsoi FastFace, translated.
 *
 * Everything here comes from the manufacturer's own demo in Fast_Face_python_demo, which is the
 * only complete description of the protocol we have - the SDK pdf documents the device's own
 * interfaces but says almost nothing about what the device sends us. Where the two disagree, the
 * demo wins, because the demo is what the vendor's cloud actually runs.
 */
class TipsoiDriver implements DeviceDriver
{
    public function vendor()
    {
        return 'tipsoi';
    }

    public function parsePunch(Request $request)
    {
        $p = $request->all();

        /* personId is what separates a recognition from a heartbeat: a heartbeat carries device
           health and no person at all. */
        $personId = $this->firstOf($p, ['personId', 'personid', 'person_id']);
        if ($personId === null || trim((string) $personId) === '') {
            return null;
        }

        /*
         * Whether the device actually matched the face.
         *
         * The two ways of getting a record from this device do not agree on how they say it. A
         * record pulled with newFindRecords carries isPass. A record the device pushes here
         * carries no isPass at all - it sends personId, aliveType, identifyType, recType and
         * type ("face_0"), and nothing else about the outcome.
         *
         * So absence must mean recognised, not unrecognised. Reading a missing field as false
         * threw away every real punch while dutifully recording it in the raw log: the face was
         * matched, the person was named, and the attendance was dropped as "not an identified
         * person". Where isPass IS sent, it is believed.
         */
        $isPass = $this->firstOf($p, ['isPass', 'ispass']);
        $recognised = ($isPass === null || $isPass === '') ? true : $this->readBool($isPass);

        return new PunchData(
            $this->firstOf($p, ['deviceKey', 'devicekey', 'DeviceKey']),
            $personId,
            $this->readTime($this->firstOf($p, ['time', 'recordTime', 'createTime'])),
            $recognised,
            $this->firstOf($p, ['name']),
            $p,
            $this->vendor()
        );
    }

    public function parseHeartbeat(Request $request)
    {
        $p = $request->all();

        return [
            'deviceKey'   => trim((string) $this->firstOf($p, ['deviceKey', 'devicekey', 'DeviceKey'])),
            'ip'          => trim((string) $this->firstOf($p, ['ip'])),
            'name'        => trim((string) $this->firstOf($p, ['deviceName'])),
            'personCount' => $this->firstOf($p, ['personCount']),
            'faceCount'   => $this->firstOf($p, ['faceCount']),
            'version'     => $this->firstOf($p, ['version']),
            'raw'         => $p,
        ];
    }

    /**
     * A queued task in the device's own language.
     *
     * The device expects ONE task object per fetch, not a list, and reads it by interfaceName.
     * Shapes taken from push_sdk_server.py.
     */
    public function formatTask(DeviceTask $task)
    {
        $payload = is_array($task->payload) ? $task->payload : [];
        $taskNo  = 'task-' . $task->id;

        switch ($task->action) {

            case 'person.create':
                return [
                    'taskNo'        => $taskNo,
                    'interfaceName' => 'person/create',
                    'result'        => true,
                    'person' => [
                        'id'                    => (string) ($payload['id'] ?? ''),
                        'name'                  => (string) ($payload['name'] ?? ''),
                        'idcardNum'             => (string) ($payload['idcardNum'] ?? ''),
                        'iDNumber'              => '',
                        /* 2 = on. Face is the only method the college uses; leaving card and the
                           rest off keeps the device from opening on anything else. */
                        'facePermission'        => 2,
                        'idCardPermission'      => 1,
                        'faceAndCardPermission' => 1,
                        'iDPermission'          => 1,
                        'tag'                   => (string) ($payload['tag'] ?? ''),
                        'phone'                 => '',
                    ],
                ];

            case 'face.createByUrl':
                /* The device downloads the photo itself. That is why enrolling 1,190 students
                   does not mean base64 encoding 1,190 images and pushing them through - we hand
                   over a link to the photo already on the site. */
                return [
                    'taskNo'        => $taskNo,
                    'interfaceName' => 'face/createByUrl',
                    'result'        => true,
                    'personId'      => (string) ($payload['personId'] ?? ''),
                    'faceId'        => (string) ($payload['faceId'] ?? ''),
                    'imgUrl'        => (string) ($payload['imgUrl'] ?? ''),
                    'isEasyWay'     => (bool) ($payload['isEasyWay'] ?? false),
                ];

            case 'person.delete':
                return [
                    'taskNo'        => $taskNo,
                    'interfaceName' => 'person/delete',
                    'result'        => true,
                    'id'            => (string) ($payload['id'] ?? ''),
                ];
        }

        /* An action nobody taught this driver. Say so plainly rather than send the device
           something it will fail on silently. */
        return [
            'taskNo'        => $taskNo,
            'interfaceName' => (string) $task->action,
            'result'        => true,
        ];
    }

    /**
     * The device does not answer true or false. It sends back its own api response for whatever
     * it just did, encoded as a json STRING inside the result field:
     *
     *   result = "{\"code\":\"LAN_SUS-0\",\"msg\":\"Personnel information added successfully\",
     *              \"result\":1,\"success\":true}"
     *
     * Read that, and believe its success flag. Testing the outer value for truth instead marks
     * every completed task as failed - the enrolment looks broken while the students are in fact
     * already on the device.
     */
    public function taskSucceeded(Request $request)
    {
        $result = $request->input('result');

        if (is_bool($result)) { return $result; }

        if (is_string($result)) {
            $trimmed = trim($result);

            /* The device's own response, wrapped in a string. */
            if ($trimmed !== '' && ($trimmed[0] === '{' || $trimmed[0] === '[')) {
                $decoded = json_decode($trimmed, true);
                if (is_array($decoded)) { $result = $decoded; }
            } else {
                return in_array(strtolower($trimmed), ['1', 'true', 'yes', 'y'], true);
            }
        }

        if (is_array($result)) {
            if (array_key_exists('success', $result)) { return (bool) $result['success']; }
            if (array_key_exists('result', $result))  { return (int) $result['result'] === 1; }
            return true;
        }

        /*
         * Nothing we can read. Call it a failure.
         *
         * Of the two ways to be wrong, this is the recoverable one. A task wrongly marked failed
         * shows up on the screen and can be sent again - the device answers "already exists" and
         * no harm is done. A task wrongly marked done leaves a student who is not on the device
         * and nobody ever finds out, until they stand at the gate and are a stranger.
         */
        return false;
    }

    public function heartbeatReply($hasTasks)
    {
        return ['result' => (bool) $hasTasks];
    }

    public function taskResultReply($hasMore)
    {
        return ['result' => (bool) $hasMore];
    }

    public function callbackConfiguration($baseUrl)
    {
        $baseUrl = rtrim($baseUrl, '/');
        $base    = config('devices.vendors.tipsoi.callback_base', '/api/face/uface5/v1');
        $out     = [];

        foreach ((array) config('devices.vendors.tipsoi.callbacks', []) as $name => $c) {
            $out[$name] = [
                'setter' => $c['setter'],
                'param'  => $c['param'],
                'url'    => $baseUrl . $base . ($c['path'] ?? ''),
                'extra'  => $c['extra'] ?? [],
            ];
        }

        return $out;
    }

    /* ---------------- helpers ---------------- */

    private function firstOf(array $payload, array $keys)
    {
        foreach ($keys as $k) {
            if (array_key_exists($k, $payload)) { return $payload[$k]; }
        }
        return null;
    }

    /**
     * The device sends a millisecond timestamp. Read it as seconds, in the app's own timezone.
     *
     * Anything unreadable becomes "now" rather than 1970 - a punch at the wrong minute is a small
     * problem, a punch dated fifty years ago quietly breaks every report that groups by day.
     */
    private function readTime($value)
    {
        $value = trim((string) $value);

        if ($value !== '' && ctype_digit($value)) {
            $seconds = strlen($value) >= 13 ? (int) substr($value, 0, 10) : (int) $value;
            if ($seconds > 946684800) {   /* anything before 2000 is not a real punch */
                return Carbon::createFromTimestamp($seconds);
            }
        }

        if ($value !== '') {
            try { return Carbon::parse($value); } catch (\Exception $e) { /* fall through */ }
        }

        return Carbon::now();
    }

    private function readBool($value)
    {
        if (is_bool($value)) { return $value; }
        $v = strtolower(trim((string) $value));
        return in_array($v, ['1', 'true', 'yes', 'y'], true);
    }
}
