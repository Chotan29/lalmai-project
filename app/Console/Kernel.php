<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //Commands\BackupDatabaseCommand::class,
       // Commands\BirthdayWish::class,
        //Commands\BalanceFeesReminder::class,
        //Commands\LibraryClearance::class,
        Commands\DatabaseBackUp::class,
        Commands\AttendanceDispatchMissing::class,
        Commands\InovaceSyncPunches::class,      // used by pipeline
        Commands\AttendanceMinutePipeline::class, // <-- add this
        Commands\GenerateRecurringBills::class,   // recurring billing auto-generator
        Commands\SmsTest::class,                  // local SMS testing
        Commands\ReconcileRegistrationPayments::class, // finishes paid-but-stuck registrations
        Commands\MergeOptionalSubjectTwins::class,     // one paper = one subject (4th-subject cleanup)
        Commands\DeactivateUnenrolledMarks::class,     // marks saved for papers a student never took
    ];

    protected function schedule(Schedule $schedule)
    {
        $tz = config('app.timezone', env('APP_TIMEZONE', 'UTC'));

        /**
         * SEQUENTIAL PIPELINE (every minute, 05:00–22:00):
         * 1) inovace:sync-punches (defaults to today inside the command)
         * 2) drain queue=attendance until empty
         * (No background; next task starts only after this finishes)
         */
        // If you want explicit range for a while, pass via options; otherwise omit to default to "today"
       $schedule->command('attendance:pipeline', [
                '--start'              => now($tz)->toDateString(),
                '--end'                => now($tz)->toDateString(),   // <- today, recomputed each minute
                '--with-notifications' => 1,                          // drain notifications right after sync => instant SMS
            ])
            ->everyMinute()
            ->between('05:00', '23:59')
            ->timezone($tz)
            ->withoutOverlapping();

        // Sweep any rows that skipped events
        $schedule->command('attendance:dispatch-missing')
            ->everyFiveMinutes()
            ->timezone($tz)
            ->withoutOverlapping();

        /**
         * Self-healing registration payments.
         * If a student paid but the callback was lost, this finishes the registration
         * automatically within minutes instead of leaving the student stranded.
         */
        $schedule->command('registration:reconcile')
            ->everyTenMinutes()
            ->timezone($tz)
            ->withoutOverlapping();

        /*
         * A short-lived worker every minute, all day, for every queue that matters.
         *
         * attendance and long are here on purpose. The pipeline above also drains attendance, but
         * only between 05:00 and 23:59 - so a Batch Update started at ten past midnight sat in
         * the queue untouched until five in the morning, with the screen showing "running" and
         * nothing reaching the device. Nobody would guess that from looking at it.
         *
         * Work queued at any hour is now picked up within the minute. --stop-when-empty keeps
         * each run short, which is what shared hosting wants.
         */
        $schedule->command('queue:work --queue=notifications,default,attendance,long --timeout=120 --tries=3 --stop-when-empty')
            ->everyMinute()
            ->timezone($tz)
            ->runInBackground()
            ->withoutOverlapping(2);

        // Restart queues daily for stability
        $schedule->command('queue:restart')
            ->dailyAt('04:00')
            ->timezone($tz);

        // Your existing daily jobs
        $schedule->command('command:birthdaywish')->daily()->timezone($tz);
        $schedule->command('command:duefeereminder')->daily()->timezone($tz);
        $schedule->command('command:libraryclearance')->daily()->timezone($tz);
        $schedule->command('integrity:semester-subject --log')
            ->dailyAt('03:30')
            ->timezone($tz)
            ->withoutOverlapping();

        // Recurring billing auto-generator — time configurable via Billing Settings
        try {
            $billSetting = \App\Models\BillingSetting::first();
            $bHour    = $billSetting ? str_pad((int) $billSetting->scheduler_hour,   2, '0', STR_PAD_LEFT) : '06';
            $bMinute  = $billSetting ? str_pad((int) $billSetting->scheduler_minute, 2, '0', STR_PAD_LEFT) : '30';
            $bEnabled = $billSetting ? (bool) $billSetting->scheduler_enabled : true;
        } catch (\Throwable $e) {
            $bHour = '06'; $bMinute = '30'; $bEnabled = true;
        }
        if ($bEnabled) {
            $schedule->command('bill:generate-recurring')
                ->dailyAt("{$bHour}:{$bMinute}")
                ->timezone($tz)
                ->withoutOverlapping()
                ->runInBackground();
        }

        // Backups
        $schedule->command('database:backup')->daily();
        $schedule->command('backup:clean')->daily()->at('05:00');
        $schedule->command('backup:run')->daily()->at('06:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
