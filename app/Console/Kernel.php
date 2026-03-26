<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * ✅ Expire Pending Orders
         * Defined in routes/console.php
         */
        $schedule->command('orders:expire')
            ->everyMinute()
            ->withoutOverlapping()
            ->onOneServer()
            ->appendOutputTo(storage_path('logs/orders_expire.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        // Load Artisan commands
        $this->load(__DIR__.'/Commands');

        // Load routes/console.php commands
        require base_path('routes/console.php');
    }
}
