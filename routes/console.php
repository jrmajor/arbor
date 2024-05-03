<?php

use App\Console\Commands;
use Illuminate\Support\Facades\Schedule;
use Laravel\Telescope\Console\PruneCommand as PruneTelescopeEntries;

Schedule::command(Commands\GenerateSitemap::class)->weekly();

Schedule::command(PruneTelescopeEntries::class)->dailyAt('01:30');
Schedule::command(Commands\MakeBackup::class)->dailyAt('02:00');
