<?php

namespace App\Console;

use App\Console\Commands\CustomerCleanup;
use App\Console\Commands\TopUpCustmerCredits;
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
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $logFile = storage_path('logs/cron.log');
        $schedule->command(CustomerCleanup::class)
            ->daily()
            ->appendOutputTo($logFile);
        $schedule->command(TopUpCustmerCredits::class)
            ->daily()
            ->appendOutputTo($logFile);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
