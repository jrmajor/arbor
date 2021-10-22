<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Finder\Finder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // $this->call(UserSeeder::class);

        $this->callSqlSeeders();
    }

    private function callSqlSeeders(): void
    {
        $seeders = (new Finder())->files()->in(__DIR__ . '/sql');

        if (! $seeders->hasResults()) {
            $this->command->line('<comment>Found no raw sql seeders.</comment>');

            return;
        }

        foreach ($seeders as $seed) {
            if ($seed->getExtension() !== 'sql') {
                continue;
            }

            $this->command->line('<comment>Seeding: </comment>' . $seed->getFilename());

            DB::unprepared($seed->getContents());

            $this->command->line('<info>Seeded:  </info>' . $seed->getFilename());
        }
    }
}
