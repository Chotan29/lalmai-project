<?php

namespace App\Providers;

use App\Services\TipsoiAttendanceService;
use Illuminate\Support\ServiceProvider;

class TipsoiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(TipsoiAttendanceService::class, function ($app) {
            return new TipsoiAttendanceService(
                $app->make(\App\Repositories\PersonRepository::class),
                $app->make(\App\Repositories\DeviceRepository::class)
            );
        });
    }

    public function boot()
    {
        //
    }
}