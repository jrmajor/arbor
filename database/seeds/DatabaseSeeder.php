<?php

use Illuminate\Database\Seeder;
use Symfony\Component\Finder\Finder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(UserSeeder::class);

        $this->callSqlSeeders();
    }

    private function callSqlSeeders()
    {
        $seeders = (new Finder)->files()->in(__DIR__.'/sql');

        if (! $seeders->hasResults()) {
            return $this->command->line('<comment>Found no raw sql seeders.</comment>');
        }

        foreach ($seeders as $seed) {
            if (! $seed->getExtension() == 'sql') {
                continue;
            }

            $this->command->line('<comment>Seeding: </comment>'.$seed->getFilename());

            DB::unprepared($seed->getContents());

            $this->command->line('<info>Seeded:  </info>'.$seed->getFilename());
        }
    }
}
