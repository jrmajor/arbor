<?php

namespace App\Services\Wielcy;

use App\Models\Person;
use Exception;
use Psl\Dict;
use Psl\Vec;

final class RelativesRepository
{
    /** @var list<string> */
    private array $ids;

    /** @var array<string, Person> */
    private array $loaded;

    public function initialize(Wielcy $wielcy): void
    {
        $ids = Vec\map([
            $wielcy->mother,
            $wielcy->father,
        ], fn (?Relative $relative): ?string => $relative?->id);

        $this->ids = Vec\values(Dict\unique_scalar(Vec\filter_nulls($ids)));
    }

    public function get(?string $id): ?Person
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

            return;
        }

        $this->loaded = Person::query()
            ->whereIn('id_wielcy', $this->ids)->get()
            ->keyBy(fn (Person $p) => $p->id_wielcy)->all();
    }
}
