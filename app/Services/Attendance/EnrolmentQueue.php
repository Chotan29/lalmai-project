<?php

namespace App\Services\Attendance;

use App\Models\DeviceTask;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

/**
 * Work waiting for a device to collect.
 *
 * The device sits behind the college wifi, so we cannot push anything to it. It asks us once a
 * minute whether there is anything to do; these rows are the answer. That is how a thousand
 * students get enrolled without anybody standing at the device.
 *
 * Vendor neutral: an action and a payload go in, and the brand's own driver turns them into
 * whatever json that device reads. Adding a second manufacturer does not change this class.
 */
class EnrolmentQueue
{
    protected $devices;

    public function __construct(DeviceManager $devices)
    {
        $this->devices = $devices;
    }

    /**
     * Enrol a student on every device that should hold them.
     *
     * This is the call the rest of the application should use. Naming a single device is how a
     * student ends up recognised at the main gate and a stranger at the back one - the same
     * mistake is easy to make once and impossible to see afterwards, because nothing looks wrong
     * until somebody walks through the wrong door.
     *
     * @return array device identifier => queued tasks
     */
    public function enrol(Student $student)
    {
        $targets = $this->devices->enrolmentTargets();

        if ($targets->isEmpty()) {
            Log::warning('Nothing to enrol onto - no device is switched on for enrolment', [
                'student' => $student->id,
            ]);
            return [];
        }

        $queued = [];
        foreach ($targets as $device) {
            $queued[$device->identifier] = $this->queueStudent($student, $this->addressFor($device));
        }

        return $queued;
    }

    /** Remove a student from every device that holds them. */
    public function withdraw($studentId)
    {
        $removed = [];
        foreach ($this->devices->enrolmentTargets() as $device) {
            $removed[$device->identifier] = $this->queueRemoval($studentId, $this->addressFor($device));
        }
        return $removed;
    }

    /**
     * Tasks are addressed by the device's own network address where it has one, and by its serial
     * otherwise - a device that has only ever been added by hand may not have reported an address
     * yet, and its work should still reach it once it does.
     */
    protected function addressFor($device)
    {
        $ip = trim((string) $device->ip_address);
        return $ip !== '' ? $ip : $device->identifier;
    }

    /**
     * Enrol one student on one device: register the person, then send the photo.
     *
     * Two tasks rather than one because the device wants them in that order - the SDK is explicit
     * that a photo can only be attached to a person who already exists.
     *
     * The photo is sent as a link, not as data. The device fetches it itself, which is the
     * difference between handing over 1,190 urls and base64 encoding 1,190 images through a
     * shared host.
     */
    public function queueStudent(Student $student, $deviceIp)
    {
        $queued = [];

        $queued[] = DeviceTask::create([
            'device_ip' => $deviceIp,
            'action'    => 'person.create',
            'payload'   => [
                'id'   => (string) $student->id,
                'name' => $this->displayName($student),
                'tag'  => (string) $student->reg_no,
            ],
            'status' => 'queued',
        ]);

        $photo = $this->photoUrl($student);
        if ($photo !== null) {
            $queued[] = DeviceTask::create([
                'device_ip' => $deviceIp,
                'action'    => 'face.createByUrl',
                'payload'   => [
                    'personId' => (string) $student->id,
                    'faceId'   => 'f' . $student->id,
                    'imgUrl'   => $photo,
                    /* Strict quality checking. A photo the device is unsure about is better
                       rejected now than found unusable at the gate on a wet morning. */
                    'isEasyWay' => false,
                ],
                'status' => 'queued',
            ]);
        }

        return $queued;
    }

    /**
     * Queue anybody at all - a student, a member of staff - onto every device that takes people.
     *
     * The device does not know or care which table somebody came from; it holds an id, a name and
     * a face. Keeping one method for both is what lets the existing Batch Update screen, which
     * was written for students and staff together, send to the device without being taken apart.
     *
     * @param  string      $personId  the id the device will report back when it sees this face
     * @param  string      $name      required by the device; it refuses a person without one
     * @param  string|null $photoUrl  an address the DEVICE can reach, or null to send no photo
     * @return array                  device identifier => queued tasks
     */
    public function enrolPerson($personId, $name, $photoUrl = null, $tag = '')
    {
        $personId = trim((string) $personId);
        $name     = trim((string) $name);

        if ($personId === '' || $name === '') {
            return [];
        }

        $queued = [];

        foreach ($this->devices->enrolmentTargets() as $device) {
            $ip = $this->addressFor($device);
            $tasks = [];

            $tasks[] = DeviceTask::create([
                'device_ip' => $ip,
                'action'    => 'person.create',
                'payload'   => ['id' => $personId, 'name' => $name, 'tag' => (string) $tag],
                'status'    => 'queued',
            ]);

            if ($photoUrl) {
                $tasks[] = DeviceTask::create([
                    'device_ip' => $ip,
                    'action'    => 'face.createByUrl',
                    'payload'   => [
                        'personId'  => $personId,
                        'faceId'    => 'f' . $personId,
                        'imgUrl'    => $photoUrl,
                        'isEasyWay' => false,
                    ],
                    'status' => 'queued',
                ]);
            }

            $queued[$device->identifier] = $tasks;
        }

        return $queued;
    }

    /** Is there any device at all waiting to be given people? */
    public function hasTargets()
    {
        return $this->devices->enrolmentTargets()->isNotEmpty();
    }

    public function queueRemoval($studentId, $deviceIp)
    {
        return DeviceTask::create([
            'device_ip' => $deviceIp,
            'action'    => 'person.delete',
            'payload'   => ['id' => (string) $studentId],
            'status'    => 'queued',
        ]);
    }

    /**
     * Students who have no photo cannot be enrolled, and it is better to know the number before
     * starting than to discover it a third of the way through.
     */
    public function studentsWithoutPhoto()
    {
        return Student::where('status', 1)
            ->where(function ($q) {
                $q->whereNull('student_image')->orWhere('student_image', '');
            })
            ->count();
    }

    /* ---------------- pieces ---------------- */

    protected function displayName(Student $student)
    {
        $name = trim(preg_replace('/\s+/', ' ',
            $student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name));

        /* The device requires a name and will refuse a person without one. */
        return $name !== '' ? $name : ('Student ' . $student->reg_no);
    }

    /**
     * The device is given a smaller copy, not the original.
     *
     * It refuses a photo over 1080 pixels tall and gives up on a slow download - and a third of
     * the photos on file are one or the other. DevicePhoto makes a copy that fits and leaves the
     * original alone, since the original is what prints on the ID card.
     */
    protected function photoUrl(Student $student)
    {
        return app(DevicePhoto::class)->urlFor('student', $student->id, $student->student_image);
    }
}
