<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sitemap:generate')->weekly();

        $schedule->command('backup:clean')->dailyAt('01:00');
        $schedule->command('backup:run')->dailyAt('02:00');
        $schedule->command('backup:monitor')->dailyAt('03:00');
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }
}
