<?php

namespace App\Services\Pytlewski;

use App\Models\Person;
use Exception;
use Psl\Dict;
use Psl\Vec;

final class RelativesRepository
{
    /** @var list<int> */
    private array $ids;

    /** @var array<int, Person> */
    private array $loaded;

    public function initialize(Pytlewski $pytlewski): void
    {
        $this->ids = Dict\unique_scalar(Vec\filter_nulls(Vec\map([
            $pytlewski->mother,
            $pytlewski->father,
            ...$pytlewski->marriages,
            ...$pytlewski->children,
            ...$pytlewski->siblings,
        ], fn (Marriage|Relative|null $relative): ?int => $relative?->id)));
    }

    public function get(?int $id): ?Person
    {
        if (! isset($this->loaded)) {
            $this->load();
        }

        return $this->loaded[$id] ?? null;
    }

    public function load(): void
    {
        if (! isset($this->ids)) {
            throw new Exception('Attempt to load relatives before initializing repository.');
        }

        if ($this->ids === []) {
            $this->loaded = [];
        }

        $this->loaded = Person::query()
            ->whereIn('id_pytlewski', $this->ids)->get()
            ->keyBy(fn (Person $p) => $p->id_pytlewski)->all();
    }
}
