<?php

namespace App\Services\Attendance;

use App\Models\TipsoiDevice;

/**
 * Keeps a record of which devices are talking to us and when they last did.
 *
 * Shared by every brand: a heartbeat is a heartbeat whoever made the hardware, and by the time
 * it reaches here the driver has already reduced it to serial, address, counts and firmware.
 *
 * Before this existed the heartbeat handler only wrote a log line - the comment in it read
 * "Save last seen?" and nobody ever answered. tipsoi_devices.connected stayed at its default of
 * zero, so the device screen reported a perfectly healthy device as inactive.
 */
class DeviceRegistry
{
    /**
     * @param  array $beat  as returned by DeviceDriver::parseHeartbeat()
     * @return TipsoiDevice
     */
    public function touch(array $beat, $requestIp = null)
    {
        /* Keyed on the serial number. The address is no good as an identifier: what we see from
           the internet is the college router, shared by everything behind it, and the device's
           own address changes whenever its lease does. */
        $key = trim((string) ($beat['deviceKey'] ?? ''));
        if ($key === '') { $key = 'ip:' . $requestIp; }

        $device = TipsoiDevice::firstOrNew(['identifier' => $key]);

        if (!$device->exists) {
            $device->status = 1;
            $device->model  = 'FastFace';
        }

        /* The address the device reports for itself, not the one the packet arrived from. */
        $lanIp = trim((string) ($beat['ip'] ?? ''));
        if ($lanIp !== '') { $device->ip_address = $lanIp; }

        $name = trim((string) ($beat['name'] ?? ''));
        if ($name !== '' && stripos($name, 'please set') === false) { $device->name = $name; }

        $device->connected = true;
        $device->last_seen = now();
        $device->save();

        return $device;
    }

    /**
     * A device that has not called in for a while is not connected, whatever it last said.
     *
     * The heartbeat is once a minute, so a few minutes of silence is a real absence rather than
     * a slow network. Called from the device screen so what it shows is current.
     */
    public function markStaleAsOffline($minutes = 5)
    {
        return TipsoiDevice::where('connected', true)
            ->where(function ($q) use ($minutes) {
                $q->whereNull('last_seen')->orWhere('last_seen', '<', now()->subMinutes($minutes));
            })
            ->update(['connected' => false]);
    }
}
