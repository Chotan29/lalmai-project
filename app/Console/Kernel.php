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
                '--with-notifications' => 0,                          // or 1 if you want to drain notifications too
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

        // (Optional) separate notifications worker (short-lived, background).
        // You can remove this if you set --with-notifications=1 in the pipeline above.
        $schedule->command('queue:work --queue=notifications,default --timeout=120 --tries=3 --stop-when-empty')
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
