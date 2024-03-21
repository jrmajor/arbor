<?php

use App\Console\Commands;
use Illuminate\Support\Facades\Schedule;
use Spatie\Backup\Commands as Backup;

Schedule::command(Commands\GenerateSitemap::class)->weekly();

Schedule::command(Backup\CleanupCommand::class)->dailyAt('01:00');
Schedule::command(Backup\BackupCommand::class)->dailyAt('02:00');
Schedule::command(Backup\MonitorCommand::class)->dailyAt('03:00');
