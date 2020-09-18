<?php

namespace App\Services\Pytlewski;

use App\Models\Person;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class Pytlewski
{
    use Concerns\ScrapesPytlewski;

    private $id;

    private $source;

    private $attributes = [];

    private $relations;

    private $keys = [
        'family_name', 'last_name', 'name', 'middle_name',
        'birth_date', 'birth_place', 'death_date', 'death_place',
        'photo', 'bio'
    ];

    private $relationKeys = [
        'mother', 'father', 'marriages', 'children', 'siblings'
    ];

    public function __construct($id)
    {
        $this->id = $id;

        $this->attributes = Cache::remember(
            'pytlewski.'.$this->id,
            CarbonInterval::week(),
            fn () => $this->scrape()
        );
    }

    private function loadRelations()
    {
        $relatives = $this->eagerLoadRelatives();

        $this->relations = $this->matchRelatives($relatives);
    }

    private function eagerLoadRelatives(): Collection
    {
        $ids = collect([
            ...$this->attributes['marriages'],
            ...$this->attributes['children'],
            ...$this->attributes['siblings'],
        ])->pluck('id')->concat([
            'mother' => $this->attributes['mother_id'] ?? null,
            'father' => $this->attributes['father_id'] ?? null,
        ])->filter();

        if ($ids->isNotEmpty()) {
            return Person::whereIn('id_pytlewski', $ids)->get();
        } else {
            return new Collection();
        }
    }

    private function matchRelatives(Collection $relatives): array
    {
        return [
            'mother' => isset($this->attributes['mother_surname']) || isset($this->attributes['mother_name'])
                ? Relative::hydrate([
                    'id' => $this->attributes['mother_id'] ?? null,
                    'person' => isset($this->attributes['mother_id']) ? $relatives
                        ->where('id_pytlewski', $this->attributes['mother_id'])->first() : null,
                    'surname' => $this->attributes['mother_surname'] ?? null,
                    'name' => $this->attributes['mother_name'] ?? null,
                ]) : null,
            'father' => isset($this->attributes['father_surname']) || isset($this->attributes['father_name'])
                ? Relative::hydrate([
                    'id' => $this->attributes['father_id'] ?? null,
                    'person' => isset($this->attributes['father_id']) ? $relatives
                        ->where('id_pytlewski', $this->attributes['father_id'])->first() : null,
                    'surname' => $this->attributes['father_surname'] ?? null,
                    'name' => $this->attributes['father_name'] ?? null,
                ]) : null,
            'marriages' => collect($this->attributes['marriages'])
                ->map(function ($marriage) use ($relatives) {
                    if (isset($marriage['id'])) {
                        $person = $relatives->where('id_pytlewski', $marriage['id'])->first();
                        $marriage['person'] = $person;
                    }

                    return Marriage::hydrate($marriage);
                }),

            'children' => collect($this->attributes['children'])
                ->map(function ($child) use ($relatives) {
                    if (isset($child['id'])) {
                        $person = $relatives->where('id_pytlewski', $child['id'])->first();
                        $child['person'] = $person;
                    }

                    return Relative::hydrate($child);
                }),

            'siblings' => collect($this->attributes['siblings'])
                ->map(function ($sibling) use ($relatives) {
                    if (isset($sibling['id'])) {
                        $person = $relatives->where('id_pytlewski', $sibling['id'])->first();
                        $sibling['person'] = $person;
                    }

                    return Relative::hydrate($sibling);
                }),

        ];
    }

    public function __get($key)
    {
        if ($key == 'id') {
            return $this->id;
        }

        if ($key == 'url') {
            return self::url($this->id);
        }

        if (in_array($key, $this->keys)) {
            return $this->attributes[$key] ?? null;
        }

        if (in_array($key, $this->relationKeys)) {
            if (! $this->relations) {
                $this->loadRelations();
            }

            return $this->relations[$key];
        }
    }

    public static function url($id): string
    {
        return 'http://www.pytlewski.pl/index/drzewo/index.php?view=true&id='.$id;
    }
}
