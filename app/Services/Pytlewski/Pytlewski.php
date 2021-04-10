<?php

namespace App\Services\Pytlewski;

use App\Models\Person;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

/**
 * @property-read int $id
 * @property-read string $url
 * @property-read ?string $family_name
 * @property-read ?string $last_name
 * @property-read ?string $name
 * @property-read ?string $middle_name
 * @property-read ?string $birth_date
 * @property-read ?string $birth_place
 * @property-read ?string $death_date
 * @property-read ?string $death_place
 * @property-read ?string $photo
 * @property-read ?string $bio
 * @property-read ?Relative $mother
 * @property-read ?Relative $father
 * @property-read Collection<Marriage> $marriages
 * @property-read Collection<Relative> $children
 * @property-read Collection<Relative> $siblings
 */
final class Pytlewski
{
    use Concerns\ScrapesPytlewski;

    protected array $attributes;

    protected ?array $relations = null;

    protected array $keys = [
        'family_name', 'last_name', 'name', 'middle_name',
        'birth_date', 'birth_place', 'death_date', 'death_place',
        'photo', 'bio',
    ];

    protected array $relationKeys = [
        'mother', 'father', 'marriages', 'children', 'siblings',
    ];

    public function __construct(
        protected int $id,
    ) {
        $this->attributes = Cache::remember(
            'pytlewski.'.$this->id,
            CarbonInterval::week(),
            fn (): array => $this->scrape(),
        );
    }

    protected function loadRelations(): void
    {
        $relatives = $this->eagerLoadRelatives();

        $this->relations = $this->matchRelatives($relatives);
    }

    protected function eagerLoadRelatives(): Collection
    {
        $ids = collect([
            ...$this->attributes['marriages'] ?? [],
            ...$this->attributes['children'] ?? [],
            ...$this->attributes['siblings'] ?? [],
        ])->pluck('id')->concat([
            'mother' => $this->attributes['mother_id'] ?? null,
            'father' => $this->attributes['father_id'] ?? null,
        ])->filter();

        return $ids->isNotEmpty()
            ? Person::whereIn('id_pytlewski', $ids)->get()
            : new Collection();
    }

    protected function matchRelatives(Collection $relatives): array
    {
        foreach (['mother', 'father'] as $relative) {
            $person = isset($this->attributes['mother_id'])
                ? $relatives->where('id_pytlewski', $this->attributes['mother_id'])->first()
                : null;

            ${$relative} = isset($this->attributes["{$relative}_surname"]) || isset($this->attributes["{$relative}_name"])
                ? Relative::hydrate([
                    'id' => $this->attributes["{$relative}_id"] ?? null,
                    'person' => $person,
                    'surname' => $this->attributes["{$relative}_surname"] ?? null,
                    'name' => $this->attributes["{$relative}_name"] ?? null,
                ])
                : null;
        }

        $marriages = collect($this->attributes['marriages'] ?? [])
            ->map(function ($marriage) use ($relatives) {
                if (isset($marriage['id'])) {
                    $marriage['person'] = $relatives->where('id_pytlewski', $marriage['id'])->first();
                }

                return Marriage::hydrate($marriage);
            });

        $children = collect($this->attributes['children'] ?? [])
            ->map(function ($child) use ($relatives) {
                if (isset($child['id'])) {
                    $child['person'] = $relatives->where('id_pytlewski', $child['id'])->first();
                }

                return Relative::hydrate($child);
            });

        $siblings = collect($this->attributes['siblings'] ?? [])
            ->map(function ($sibling) use ($relatives) {
                if (isset($sibling['id'])) {
                    $sibling['person'] = $relatives->where('id_pytlewski', $sibling['id'])->first();
                }

                return Relative::hydrate($sibling);
            });

        return compact('mother', 'father', 'marriages', 'children', 'siblings');
    }

    public static function url(int $id): string
    {
        return "http://www.pytlewski.pl/index/drzewo/index.php?view=true&id={$id}";
    }

    public function __get(string $key): mixed
    {
        if ($key === 'id') {
            return $this->id;
        }

        if ($key === 'url') {
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

        throw new InvalidArgumentException("Key [{$key}] does not exist.");
    }
}
