<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\DeviceTask;
use App\Services\Attendance\DeviceManager;
use App\Services\Attendance\EnrolmentQueue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Put every active student on the attendance device.
 *
 * Nothing is sent to the device from here. Work is queued in device_tasks and the device collects
 * it - it asks once a minute whether there is anything, and once there is, it takes one task,
 * reports back, and immediately asks for the next. So a thousand students go across steadily
 * without anybody standing at the machine and without the server ever having to reach into the
 * college network.
 *
 * Two tasks per student, in this order, because the SDK will not attach a photo to a person who
 * does not exist yet:
 *     person/create      the student's own id and name
 *     face/createByUrl   a link to the photo already on the site, which the device downloads
 *
 * Safe to run twice. A student who already has a completed person/create for that device is
 * skipped, so an interrupted run can simply be run again.
 *
 *   php artisan attendance:enrol-students --check      what would happen, and which photos fail
 *   php artisan attendance:enrol-students              queue everybody who is not already done
 *   php artisan attendance:enrol-students --limit=20   a small batch first
 */
class EnrolStudentsToDevice extends Command
{
    protected $signature = 'attendance:enrol-students
        {--check : report only, queue nothing}
        {--limit=0 : queue at most this many students}
        {--faculty= : only this faculty, by name}
        {--again : queue students that were already enrolled}';

    protected $description = 'Queue active students for enrolment on the attendance devices.';

    public function handle(DeviceManager $devices, EnrolmentQueue $queue)
    {
        $check   = (bool) $this->option('check');
        $limit   = (int) $this->option('limit');
        $again   = (bool) $this->option('again');
        $faculty = trim((string) $this->option('faculty'));

        $targets = $devices->enrolmentTargets();
        if ($targets->isEmpty()) {
            $this->error('No device is switched on for enrolment. Add one first, or set enrol_students.');
            return 1;
        }

        $this->info('Devices that will receive students:');
        foreach ($targets as $d) {
            $this->line(sprintf('  %-24s %-16s %s', $d->identifier, $d->ip_address ?: '(no ip)', $d->name));
        }

        /* ---------- who ---------- */
        $students = Student::where('status', 1)
            ->when($faculty !== '', function ($q) use ($faculty) {
                $q->whereIn('faculty', function ($sub) use ($faculty) {
                    $sub->select('id')->from('faculties')->where('faculty', $faculty);
                });
            })
            ->orderBy('id')
            ->get(['id', 'reg_no', 'first_name', 'middle_name', 'last_name', 'student_image']);

        $this->info("\nActive students to consider: " . $students->count());

        /* Already done: a person/create that the device confirmed. */
        $done = [];
        if (!$again) {
            foreach (DeviceTask::where('action', 'person.create')->where('status', 'done')
                ->get(['payload']) as $t) {
                $p = is_array($t->payload) ? $t->payload : [];
                if (!empty($p['id'])) { $done[(string) $p['id']] = true; }
            }
        }

        /* ---------- the photos ---------- */
        $noPhoto = [];
        $tooSmall = [];
        $tooBig = [];
        $ready = [];

        foreach ($students as $s) {
            if (!$again && isset($done[(string) $s->id])) { continue; }

            $file = trim((string) $s->student_image);
            if ($file === '') { $noPhoto[] = $s; continue; }

            $path = public_path('images/studentProfile/' . $file);
            if (!is_file($path)) { $noPhoto[] = $s; continue; }

            /* The SDK is specific: larger than 112x112, no taller than 1080, under 2 MB. A photo
               that fails these is refused by the device, and finding that out one student at a
               time across a thousand is not a good use of anyone's afternoon.

               Too tall or too heavy is no longer a reason to skip anybody: DevicePhoto makes a
               smaller copy for the device on the way out. Too small still is - there is no way to
               add detail that was never photographed, and the device will refuse it. */
            $size = @getimagesize($path);
            if (!$size || $size[0] <= 112 || $size[1] <= 112) { $tooSmall[] = $s; continue; }

            $ready[] = $s;
        }

        $this->info("\nphotos:");
        $this->line('  ready to send      : ' . count($ready));
        $this->line('  already enrolled   : ' . (count($students) - count($ready) - count($noPhoto) - count($tooSmall) - count($tooBig)));
        $this->line('  no photo on file   : ' . count($noPhoto));
        $this->line('  too small for the device : ' . count($tooSmall));
        $this->line('  too large for the device : ' . count($tooBig) . '  (resized on the way out)');

        foreach ([['no photo', $noPhoto], ['too small', $tooSmall], ['too large', $tooBig]] as $pair) {
            list($label, $list) = $pair;
            if (!$list) { continue; }
            $this->line("\n  first few " . $label . ':');
            foreach (array_slice($list, 0, 8) as $s) {
                $this->line(sprintf('    %-10s %s', $s->reg_no,
                    trim($s->first_name . ' ' . $s->last_name)));
            }
            if (count($list) > 8) { $this->line('    ... and ' . (count($list) - 8) . ' more'); }
        }

        if ($limit > 0) { $ready = array_slice($ready, 0, $limit); }

        $tasks = count($ready) * 2 * $targets->count();
        $this->info("\nwould queue " . $tasks . ' task(s) for ' . count($ready) . ' student(s)');
        $this->line('  at roughly a second each the device would work through them in about '
            . gmdate('H:i:s', $tasks));

        if ($check) {
            $this->info("\nNothing queued. Run without --check to send them.");
            return 0;
        }

        if (!$ready) {
            $this->info('Nothing to do.');
            return 0;
        }

        /* ---------- queue ---------- */
        $bar = $this->output->createProgressBar(count($ready));
        $bar->start();

        $queued = 0;
        foreach (array_chunk($ready, 100) as $chunk) {
            /* One transaction per hundred rather than one for everything: this account has
               twenty-five database connections and a single long transaction across a thousand
               students would hold one of them for the whole run. */
            DB::transaction(function () use ($chunk, $queue, &$queued, $bar) {
                foreach ($chunk as $s) {
                    $queue->enrol($s);
                    $queued++;
                    $bar->advance();
                }
            });
        }

        $bar->finish();
        $this->info("\n\nqueued " . $queued . ' student(s)');
        $this->line('  waiting in device_tasks: ' . DeviceTask::where('status', 'queued')->count());
        $this->line("\nThe device collects these on its own. Watch the count fall.");

        return 0;
    }
}
