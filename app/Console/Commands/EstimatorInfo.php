<?php

namespace App\Console\Commands;

use App\Models\Person;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EstimatorInfo extends Command
{
    protected $signature = 'estimator:info';

    protected $description = 'Get statistics about people age estimation quality.';

    public function handle()
    {
        $initialLazyLoadingMode = Model::preventsLazyLoading();

        Model::preventLazyLoading(false);

        $time = microtime(true);

        $people = Person::query()
            ->whereNotNull('birth_date_from')
            ->get()
            ->filter(fn (Person $person) => $person->birth_year)
            ->map(fn (Person $person) => (object) [
                'personId' => $person->id,
                'error' => $person->estimatedBirthDateError(),
            ])
            ->whereNotNull('error')
            ->sortByDesc('error');

        $first = $people->first();
        $maximalError = "{$first->error} (person №{$first->personId})";

        $averageError = $people->avg('error');

        $variance = $people
            ->map(fn ($data) => ($data->error - $averageError) ** 2)
            ->avg();

        $generationInterval = Person::query()
            ->whereNotNull('birth_date_from')
            ->where(fn (Builder $q) => $q->whereHas('father')->orWhereHas('mother'))
            ->get()
            ->filter(fn (Person $person) => $person->birth_year)
            ->map(fn (Person $person) => [
                ($f = $person->father?->birth_year) ? $person->birth_year - $f : null,
                ($m = $person->mother?->birth_year) ? $person->birth_year - $m : null,
            ])
            ->flatten()
            ->filter()
            ->avg();

        $this->table([], [
            ['maximal error', $maximalError],
            ['average error', round($averageError, 2)],
            ['variance', round($variance, 2)],
            ['standard deviation', round(sqrt($variance), 2)],
            ['real interval', round($generationInterval, 2)],
            ['used interval', Person::generationInterval],
        ]);

        $time = microtime(true) - $time;

        $this->comment("Calculated in {$time}");

        Model::preventLazyLoading($initialLazyLoadingMode);
    }
}
