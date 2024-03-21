<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Psl\File;
use Psl\Filesystem;
use Psl\Json;
use Psl\Vec;
use stdClass;

class DbExport extends Command
{
    protected $signature = 'db:export';

    public function handle(): void
    {
        $dir = storage_path('export-' . date('Y-m-d') . '-' . time());
        Filesystem\create_directory($dir);

        $tables = Vec\flat_map(
            DB::select('show tables'),
            fn (stdClass $r) => (array) $r,
        );

        foreach ($tables as $table) {
            $data = DB::table($table)->get();

            File\write("{$dir}/{$table}.json", Json\encode($data, true));
        }
    }
}
