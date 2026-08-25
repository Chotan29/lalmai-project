<?php

use Monolog\Handler\StreamHandler;

/*
 * Logging.
 *
 * This file was missing. Laravel needs it to build a logger, and without it every call to Log
 * fell through to the emergency logger with "Unable to create configured logger. Log [] is not
 * defined" - which happens to write to laravel.log during a web request, so nobody noticed, but
 * throws inside a queue worker where there is no request to fall back on.
 *
 * That is how a failed text message came to leave no trace. The sms gateway answered clearly -
 * "Insufficient SMS balance" - the code called Log::error to record it, Log::error itself threw,
 * and all that reached the attendance row was the word sms_send_failed. With a thousand messages
 * a morning, that is the difference between knowing why something did not arrive and guessing.
 *
 * Kept deliberately close to what the application already did: the default channel writes to
 * storage/logs/laravel.log, the same file as before, so nothing that reads it has to change.
 */

return [

    'default' => env('LOG_CHANNEL', 'stack'),

    'channels' => [

        /*
         * One file per day rather than one file for ever.
         *
         * laravel.log on live had reached 279 MB by 19 August - on shared hosting, where disk is
         * limited and every write appends to the whole thing. Daily files with a fortnight kept
         * means the log stays useful and stops quietly eating the account.
         *
         * The name is still laravel.log; the daily driver adds the date, so today's entries land
         * in laravel-YYYY-MM-DD.log and the old single file is left where it is rather than
         * deleted - it is the only record of what happened before today.
         */
        'stack' => [
            'driver'            => 'stack',
            'channels'          => ['daily'],
            'ignore_exceptions' => false,
        ],

        /* The file the application has always written to. */
        'single' => [
            'driver' => 'single',
            'path'   => storage_path('logs/laravel.log'),
            'level'  => env('LOG_LEVEL', 'debug'),
        ],

        /*
         * One file per day, kept for a fortnight. Not the default only because laravel.log is
         * what everything currently reads; worth switching to once the device is running, since
         * a single file that never rotates is already several megabytes and attendance will add
         * to it every minute of every school day.
         */
        'daily' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/laravel.log'),
            'level'  => env('LOG_LEVEL', 'debug'),
            'days'   => env('LOG_DAILY_DAYS', 14),
        ],

        'stderr' => [
            'driver'    => 'monolog',
            'handler'   => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with'      => ['stream' => 'php://stderr'],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level'  => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level'  => env('LOG_LEVEL', 'debug'),
        ],

        /*
         * Laravel writes here when it cannot build any of the above. Given the whole point of
         * this file is that it was missing, leave the fallback somewhere findable.
         */
        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

    ],

];
