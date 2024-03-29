<?php

namespace App\Console;

use App\Console\Commands\CustomerCleanup;
use App\Console\Commands\TopUpCustomerCredits;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $logFile = storage_path('logs/cron.log');
        $schedule->command(CustomerCleanup::class)
            ->daily()
            ->appendOutputTo($logFile);
        $schedule->command(TopUpCustomerCredits::class)
            ->daily()
            ->appendOutputTo($logFile);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
