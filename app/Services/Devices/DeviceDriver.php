<?php

namespace App\Services\Devices;

use App\Models\DeviceTask;
use Illuminate\Http\Request;

/**
 * What every attendance device brand has to provide.
 *
 * The application talks to devices only through this. A driver's whole job is translation: the
 * manufacturer's json in one direction, ours in the other. It holds no attendance rules, writes
 * no attendance rows and sends no messages - those belong to App\Services\Attendance, shared by
 * every brand.
 *
 * Adding a second manufacturer means writing one of these, a thin controller, a route group and
 * a block in config/devices.php. Nothing else in the application is touched.
 */
interface DeviceDriver
{
    /** The key this brand is known by in config/devices.php. */
    public function vendor();

    /**
     * A recognition callback, turned into our own shape.
     *
     * Returns null when the request is not a recognition event at all - some devices post
     * heartbeats and recognitions to the same address, so the driver decides which is which.
     */
    public function parsePunch(Request $request);

    /**
     * A heartbeat, reduced to the handful of fields worth recording: serial, the device's own
     * address, how many people and faces it holds, firmware.
     */
    public function parseHeartbeat(Request $request);

    /**
     * One of our queued tasks, written the way this device expects to read it.
     */
    public function formatTask(DeviceTask $task);

    /**
     * Did the device say the task worked?
     *
     * Belongs to the driver because brands report this differently and the difference is easy to
     * get wrong in a way nothing catches: Tipsoi answers with its whole api response encoded as a
     * json STRING, not a boolean, so a naive truth test reads every success as a failure and the
     * queue fills with tasks that in fact completed.
     */
    public function taskSucceeded(Request $request);

    /**
     * What to answer a heartbeat with. Tipsoi wants {"result": true} to mean "come and fetch
     * work"; another brand may want something else entirely, so it is asked rather than assumed.
     */
    public function heartbeatReply($hasTasks);

    /**
     * What to answer after the device reports a task done. For Tipsoi, true means "there is more,
     * fetch the next one straight away" - answer false when there is nothing left or the device
     * will wait a full minute for work it could have had now.
     */
    public function taskResultReply($hasMore);

    /**
     * The addresses to write into the device so it starts calling us, as
     * setter endpoint => [param name, full url, any extra fields].
     *
     * Used once, from a machine on the same network as the device. After that the device keeps
     * calling on its own and nobody needs to be near it again.
     */
    public function callbackConfiguration($baseUrl);
}
