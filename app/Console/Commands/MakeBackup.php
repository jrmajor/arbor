<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Psl\Filesystem;

class MakeBackup extends Command
{
    protected $signature = 'backup:run';

    protected $description = 'Make a new backup.';

    public function handle(): void
    {
        $databasePath = DB::getConfig()['database'];
        $databaseSize = $this->humanReadableSizeOf($databasePath);
        $backupName = date('Y-m-d-H-i-s') . '.sqlite';

        $this->line("Backing up database to {$backupName} ({$databaseSize})...");

        Storage::disk('backup')->putFileAs('database', $databasePath, $backupName);

        $this->info('Backup successfull.');
    }

    /**
     * @param non-empty-string $path
     */
    private function humanReadableSizeOf(string $path): string
    {
        $sizeInBytes = Filesystem\file_size($path);

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        if ($sizeInBytes === 0) {
            return '0 ' . $units[1];
        }

        for ($i = 0; $sizeInBytes > 1024; $i++) {
            $sizeInBytes /= 1024;
        }

        return round($sizeInBytes, 2) . ' ' . $units[$i];
    }
}
