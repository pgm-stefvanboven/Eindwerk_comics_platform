<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\RemoveExpiredRentals;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        RemoveExpiredRentals::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Schedule the command to run daily
        $schedule->command('rentals:remove-expired')->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
