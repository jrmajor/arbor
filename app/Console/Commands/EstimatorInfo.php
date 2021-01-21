<?php

namespace App\Console\Commands;

use App\Models\Person;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;

class EstimatorInfo extends Command
{
    protected $signature = 'estimator:info';

    protected $description = 'Get statistics about people age estimation quality.';

    public function handle()
    {
        $people = Person::whereNotNull('birth_date_from')->get()
            ->filter(fn ($person) => $person->birth_year)
            ->map(fn ($person) => (object) [
                'person' => $person,
                'error' => $person->estimatedBirthDateError(),
            ])->whereNotNull('error')
            ->sortByDesc->error;

        $generationInterval = Person::whereNotNull('birth_date_from')
            ->whereNotNull('father_id')
            ->union(
                Person::whereNotNull('birth_date_from')
                    ->whereNotNull('father_id'),
            )->get()
            ->filter(fn ($person) => $person->birth_year)
            ->map(fn ($person) => [
                $person->mother?->birth_year
                    ? $person->birth_year - $person->mother->birth_year
                    : null,
                $person->father?->birth_year
                    ? $person->birth_year - $person->father->birth_year
                    : null,
            ])->flatten()->avg();

        (new Table($this->output))->setRows([
            [
                'minimal error',
                $people->first()->error.' (person №'.$people->first()->person->id.')',
            ],
            [
                'maximal error',
                $people->reverse()->first()->error.' (person №'.$people->reverse()->first()->person->id.')',
            ],
            [
                'average error',
                round($averageError = $people->avg->error, 2),
            ],
            [
                'variance',
                round(
                    $variance = $people
                        ->map(fn ($data) => ($data->error - $averageError) ** 2)
                        ->avg(),
                    2,
                ),
            ],
            [
                'standard deviation',
                round(sqrt($variance), 2),
            ],
            [
                'interval',
                round($generationInterval, 2).' (using '.Person::generationInterval.')',
            ],
        ])->setStyle('default')
        ->render();
    }
}
