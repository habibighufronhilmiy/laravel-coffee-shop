<?php

namespace App\Providers;

use App\Console\Commands\CancelExpiredOrders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command(CancelExpiredOrders::class)->everyMinute();
        });
    }
}
