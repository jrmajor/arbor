<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Psl\File;
use Psl\Json;
use Psl\Vec;
use Symfony\Component\Finder\Finder;

class DbImport extends Command
{
    protected $signature = 'db:import {path}';

    public function handle(): void
    {
        $files = (new Finder())->in($this->argument('path'))->files();

        foreach ($files as $file) {
            $table = $file->getBasename('.json');
            $data = Json\decode(File\read($file->getPathname()));

            $this->info("Importing {$table} table...");

            $this->withProgressBar(
                /** @phpstan-ignore-next-line */
                Vec\chunk($data, 200),
                fn (array $d) => DB::table($table)->insert($d),
            );

            $this->newLine();
        }
    }
}
