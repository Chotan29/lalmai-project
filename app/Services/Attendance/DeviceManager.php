<?php

namespace App\Services\Attendance;

use App\Models\TipsoiDevice;
use App\Services\Devices\DeviceDriver;
use Illuminate\Support\Facades\Log;

/**
 * The college's devices - adding them, listing them, and knowing which driver each one speaks.
 *
 * Separate from DeviceRegistry on purpose. That one is written to by the devices themselves,
 * once a minute, and does nothing but record that a device is alive. This one is written to by
 * people: a device is bought, mounted at the back gate, given a name, and later taken down.
 *
 * Brand-neutral. A row carries its own vendor, and every lookup here goes through
 * config/devices.php to find the driver - so a Hikvision at the hostel and a FastFace at the gate
 * sit in the same table and the rest of the application never has to care.
 */
class DeviceManager
{
    /**
     * Add a device, or update it if that serial is already known.
     *
     * Keyed on the serial rather than the address, because addresses move. If the device has
     * already introduced itself by heartbeat this fills in the things only a person knows -
     * where it is, what to call it, whether students should be pushed to it.
     */
    public function add(array $attributes)
    {
        $identifier = trim((string) ($attributes['identifier'] ?? ''));
        if ($identifier === '') {
            throw new \InvalidArgumentException('A device needs its serial number - the deviceKey it reports.');
        }

        $vendor = trim((string) ($attributes['vendor'] ?? config('devices.default', 'tipsoi')));
        if (!$this->isKnownVendor($vendor)) {
            throw new \InvalidArgumentException("No driver is configured for '{$vendor}'. Add it to config/devices.php first.");
        }

        $device = TipsoiDevice::firstOrNew(['identifier' => $identifier]);

        $device->vendor         = $vendor;
        $device->name           = trim((string) ($attributes['name'] ?? $device->name ?? 'Device'));
        $device->ip_address     = trim((string) ($attributes['ip_address'] ?? $device->ip_address ?? ''));
        $device->location       = trim((string) ($attributes['location'] ?? $device->location ?? ''));
        $device->notes          = trim((string) ($attributes['notes'] ?? $device->notes ?? ''));
        $device->model          = trim((string) ($attributes['model'] ?? $device->model ?? ''));
        /*
         * Read the stored value, not $device->status.
         *
         * BaseModel puts an accessor on status that turns 1 into the string 'active', so
         * (int) $device->status is (int) 'active', which is 0 - and every device would be added
         * switched off. The raw attribute is the number actually in the row.
         */
        $device->status = array_key_exists('status', $attributes)
            ? (int) $attributes['status']
            : $this->storedStatus($device);
        $device->enrol_students = array_key_exists('enrol_students', $attributes)
            ? (bool) $attributes['enrol_students']
            : (bool) ($device->exists ? $device->enrol_students : true);

        $device->save();

        return $device;
    }

    /**
     * Tell the device where to call, so it starts reporting to this application.
     *
     * This is the one action that reaches out to the device rather than waiting for it, and the
     * one place the direction of travel matters: it needs to open a connection to
     * http://<device ip>:8090, which only works from a machine on the same network. Run from
     * shared hosting it will simply time out - the college's router does not let the internet in.
     * So it is done once, from a computer at the college, and after that the device calls us for
     * ever without anyone going near it.
     *
     * The addresses written in are built from OUR url. Nothing is hardcoded: move the application
     * to a different domain and reconnecting the device is enough.
     *
     * @param  string $baseUrl  where the device should call, e.g. https://ims.lalmaigc.edu.bd
     * @return array            per callback: whether the device accepted it
     */
    public function connect(TipsoiDevice $device, $baseUrl = null, $password = null)
    {
        $ip = trim((string) $device->ip_address);
        if ($ip === '') {
            throw new \InvalidArgumentException('This device has no network address yet, so it cannot be reached.');
        }

        $baseUrl = trim((string) ($baseUrl ?: config('app.url')));
        if ($baseUrl === '') {
            throw new \InvalidArgumentException('No address to give the device - set APP_URL, or pass one in.');
        }

        $driver = $this->driverFor($device);
        $client = app(\App\Services\Tipsoi\SdkClient::class);

        $results = [];
        foreach ($driver->callbackConfiguration($baseUrl) as $name => $c) {
            $params = array_merge([$c['param'] => $c['url']], $c['extra']);
            $res    = $client->setCallback($ip, $c['setter'], $params, $password);

            $accepted = (bool) ($res['json']['success'] ?? false);

            $results[$name] = [
                'setter'   => $c['setter'],
                'url'      => $c['url'],
                'accepted' => $accepted,
                'message'  => $res['json']['msg'] ?? ($res['error'] ?? 'no reply'),
            ];

            if (!$accepted) {
                Log::warning('Device refused a callback address', [
                    'device' => $device->identifier, 'setter' => $c['setter'], 'reply' => $res,
                ]);
            }
        }

        return $results;
    }

    /**
     * What the device says it is currently pointed at.
     *
     * Worth reading before changing anything - it is the only record of where a device was
     * sending its punches, and there is no other copy once it is overwritten.
     */
    public function readCallbacks(TipsoiDevice $device, $password = null)
    {
        $ip = trim((string) $device->ip_address);
        if ($ip === '') { return null; }

        $res = app(\App\Services\Tipsoi\SdkClient::class)->getCallbacks($ip, $password);

        return $res['json']['data'] ?? null;
    }

    /**
     * The status as it sits in the row, past the accessor. A device being added for the first
     * time is in service.
     */
    protected function storedStatus(TipsoiDevice $device)
    {
        $raw = $device->getAttributes();

        return ($device->exists && array_key_exists('status', $raw)) ? (int) $raw['status'] : 1;
    }

    /**
     * Take a device out of service without losing what it recorded.
     *
     * Deliberately not a delete. Its punches are somebody's attendance and its identifier is on
     * rows in tipsoi_attendance_logs; removing the row would orphan all of it. Switched off, it
     * stops receiving students and stops being counted as missing when it goes quiet.
     */
    public function retire($identifier)
    {
        $device = TipsoiDevice::where('identifier', $identifier)->first();
        if (!$device) { return null; }

        $device->status         = 0;
        $device->enrol_students = false;
        $device->connected      = false;
        $device->save();

        return $device;
    }

    /** Every device in service, whatever the brand. */
    public function active()
    {
        return TipsoiDevice::where('status', 1)->orderBy('id')->get();
    }

    /**
     * The devices a student should be pushed to.
     *
     * This is why the manager exists. With one device the caller could name it; with three, every
     * enrolment has to reach all of them or a student walks up to the back gate and is a stranger.
     */
    public function enrolmentTargets()
    {
        return TipsoiDevice::where('status', 1)->where('enrol_students', true)->orderBy('id')->get();
    }

    /**
     * Devices that have stopped calling in.
     *
     * A device reports every minute, so silence is real. Worth surfacing on the dashboard: a
     * reader that quietly died on Thursday means nobody who used that door has attendance since.
     */
    public function silent($minutes = 5)
    {
        return TipsoiDevice::where('status', 1)
            ->where(function ($q) use ($minutes) {
                $q->whereNull('last_seen')->orWhere('last_seen', '<', now()->subMinutes($minutes));
            })
            ->orderBy('last_seen')
            ->get();
    }

    /**
     * The driver that understands this device.
     *
     * @return DeviceDriver
     */
    public function driverFor(TipsoiDevice $device)
    {
        return $this->driverForVendor($device->vendor ?: config('devices.default', 'tipsoi'));
    }

    /** @return DeviceDriver */
    public function driverForVendor($vendor)
    {
        $class = config("devices.vendors.{$vendor}.driver");

        if (!$class || !class_exists($class)) {
            Log::error('No driver for device vendor', ['vendor' => $vendor]);
            throw new \RuntimeException("No driver configured for device vendor '{$vendor}'.");
        }

        return app($class);
    }

    public function isKnownVendor($vendor)
    {
        return (bool) config("devices.vendors.{$vendor}.driver");
    }

    /** Every brand the application can currently talk to, for a dropdown. */
    public function vendorOptions()
    {
        $out = [];
        foreach ((array) config('devices.vendors', []) as $key => $cfg) {
            $out[$key] = $cfg['label'] ?? $key;
        }
        return $out;
    }
}
