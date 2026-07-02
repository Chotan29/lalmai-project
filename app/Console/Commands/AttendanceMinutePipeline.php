<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceMinutePipeline extends Command
{
    protected $signature = 'attendance:pipeline {--start=} {--end=} {--with-notifications=0}';
    protected $description = 'Sequential: sync punches, then drain attendance queue (and optionally notifications).';

    public function handle(): int
    {
        $tz    = config('app.timezone', 'Asia/Kathmandu');
        $today = Carbon::now($tz)->toDateString();

        //$start = $this->option('start') ?: $today;
        $start = $this->option('start') ?: $today;
        $end   = $this->option('end')   ?: $today;

        // Normalize dates (YYYY-MM-DD)
        try {
            $start = Carbon::parse($start, $tz)->toDateString();
            $end   = Carbon::parse($end,   $tz)->toDateString();
        } catch (\Throwable $e) {
            $this->error('Invalid --start/--end (use YYYY-MM-DD).');
            return 1;
        }
        if (Carbon::parse($end, $tz)->lt(Carbon::parse($start, $tz))) {
            $this->error('--end must be the same day or after --start.');
            return 1;
        }

        // 1) ENQUEUE sync run (same as UI path)
        $this->info("Step 1/2: inovace:sync-punches {$start}..{$end}");
        Artisan::call('inovace:sync-punches', ['--start' => $start, '--end' => $end]);
        $this->line(Artisan::output());

        // 2) DRAIN attendance queue (sequential)
        $this->info('Step 2/2: draining attendance queue');
        if (!$this->drainQueue('attendance')) {
            $this->warn('`--stop-when-empty` unsupported; using --once loop.');
            $this->drainQueueOnceLoop('attendance', 300); // up to 300 jobs per run
        }

        // Optional: notifications
        if ((int)$this->option('with-notifications') === 1) {
            $this->info('Draining notifications queue');
            if (!$this->drainQueue('notifications,default')) {
                $this->warn('`--stop-when-empty` unsupported; using --once loop for notifications.');
                $this->drainQueueOnceLoop('notifications,default', 300);
            }
        }

        $this->info('Pipeline done.');
        return 0;
    }

    /**
     * Try to drain using --stop-when-empty (Laravel >= 8.47).
     * Returns true if the command executed; false if the option isn’t supported.
     */
    private function drainQueue(string $queues): bool
    {
        try {
            Artisan::call('queue:work', [
                '--queue'           => $queues,
                '--timeout'         => 120,
                '--tries'           => 3,
                '--stop-when-empty' => true,
            ]);
            $this->line(Artisan::output());
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Drain by repeatedly calling --once until queue is empty or limit reached.
     */
    private function drainQueueOnceLoop(string $queues, int $maxIterations = 300): void
    {
        for ($i = 0; $i < $maxIterations; $i++) {
            $pending = $this->pendingJobsCount($queues);
            if ($pending === 0) break;

            Artisan::call('queue:work', [
                '--queue'   => $queues,
                '--timeout' => 120,
                '--tries'   => 3,
                '--once'    => true,
            ]);
            // Optional: $this->line(Artisan::output());
        }
    }

    private function pendingJobsCount(string $queues): int
    {
        $list = array_map('trim', explode(',', $queues));
        return (int) DB::table('jobs')->whereIn('queue', $list)->count();
    }
}
