<?php

use App\Console\Commands;
use Illuminate\Support\Facades\Schedule;
use Laravel\Telescope\Console\PruneCommand as PruneTelescopeEntries;

Schedule::command(PruneTelescopeEntries::class)
    ->dailyAt('01:30')
    ->graceTimeInMinutes(15);

Schedule::command(Commands\MakeBackup::class)
    ->dailyAt('02:00')
    ->graceTimeInMinutes(15);
