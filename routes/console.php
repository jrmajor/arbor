<?php

use App\Console\Commands;
use Illuminate\Support\Facades\Schedule;

Schedule::command(Commands\GenerateSitemap::class)->weekly();

Schedule::command(Commands\MakeBackup::class)->dailyAt('02:00');
