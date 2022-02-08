<?php

namespace App\Services\Pytlewski;

use App\Models\Person;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Psl\Vec;

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
 * @property-read list<Marriage> $marriages
 * @property-read list<Relative> $children
 * @property-read list<Relative> $siblings
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

    protected function __construct(
        protected int $id,
    ) {
        $this->attributes = Cache::remember(
            "pytlewski.{$this->id}",
            CarbonInterval::week(),
            fn (): array => $this->scrape(),
        );
    }

    public static function find(int $id): ?self
    {
        $pytlewski = new self($id);

        return $pytlewski->exists() ? $pytlewski : null;
    }

    protected function exists(): bool
    {
        return $this->name || $this->family_name || $this->last_name;
    }

    protected function loadRelations(): void
    {
        $relatives = $this->eagerLoadRelatives();

        $this->relations = $this->matchRelatives($relatives);
    }

    /**
     * @return Collection<int, Person>
     */
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

    /**
     * @param Collection<int, Person> $relatives
     */
    protected function matchRelatives(Collection $relatives): array
    {
        $mother = isset($this->attributes['mother_surname']) || isset($this->attributes['mother_name'])
            ? new Relative(
                id: $this->attributes['mother_id'] ?? null,
                name: $this->attributes['mother_name'] ?? null,
                surname: $this->attributes['mother_surname'] ?? null,
                person: isset($this->attributes['mother_id']) ? $relatives
                    ->where('id_pytlewski', $this->attributes['mother_id'])->first() : null,
            ) : null;

        $father = isset($this->attributes['father_surname']) || isset($this->attributes['father_name'])
            ? new Relative(
                id: $this->attributes['father_id'] ?? null,
                name: $this->attributes['father_name'] ?? null,
                surname: $this->attributes['father_surname'] ?? null,
                person: isset($this->attributes['father_id']) ? $relatives
                    ->where('id_pytlewski', $this->attributes['father_id'])->first() : null,
            ) : null;

        $marriages = Vec\map($this->attributes['marriages'] ?? [], function ($marriage) use ($relatives) {
            if (isset($marriage['id'])) {
                $marriage['person'] = $relatives->where('id_pytlewski', $marriage['id'])->first();
            }

            return new Marriage(...$marriage);
        });

        $children = Vec\map($this->attributes['children'] ?? [], function ($child) use ($relatives) {
            if (isset($child['id'])) {
                $child['person'] = $relatives->where('id_pytlewski', $child['id'])->first();
            }

            return new Relative(...$child);
        });

        $siblings = Vec\map($this->attributes['siblings'] ?? [], function ($sibling) use ($relatives) {
            if (isset($sibling['id'])) {
                $sibling['person'] = $relatives->where('id_pytlewski', $sibling['id'])->first();
            }

            return new Relative(...$sibling);
        });

        return compact('mother', 'father', 'marriages', 'children', 'siblings');
    }

    public static function url(int $id): string
    {
        return "http://www.pytlewski.pl/index/drzewo/index.php?view=true&id={$id}";
    }

    public function __get(string $key): mixed
    {
        if (in_array($key, $this->relationKeys) && ! $this->relations) {
            $this->loadRelations();
        }

        return match (true) {
            $key === 'id' => $this->id,
            $key === 'url' => self::url($this->id),
            in_array($key, $this->keys) => $this->attributes[$key] ?? null,
            in_array($key, $this->relationKeys) => $this->relations[$key],
            default => throw new InvalidArgumentException("Key [{$key}] does not exist."),
        };
    }
}
