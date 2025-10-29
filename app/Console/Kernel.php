<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Fermer automatiquement les présences sans check-out à minuit
        $schedule->command('attendance:auto-close')
            ->dailyAt('00:00')
            ->withoutOverlapping()
            ->onSuccess(function () {
                \Log::info('Auto-close attendances executed successfully');
            })
            ->onFailure(function () {
                \Log::error('Auto-close attendances failed');
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
