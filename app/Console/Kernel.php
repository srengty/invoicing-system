<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Check and update payment statuses daily at 00:01 AM
        $schedule->command('payments:check-statuses')
                 ->dailyAt('00:01')
                 ->timezone('Asia/Phnom_Penh') // Adjust to your timezone
                 ->description('Update payment schedules to PAST DUE when overdue');

        // You can add other scheduled commands here
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
