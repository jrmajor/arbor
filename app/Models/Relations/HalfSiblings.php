<?php

namespace App\Models\Relations;

use App\Enums\Sex;
use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends Relation<Person, Person, Collection<int, Person>>
 */
class HalfSiblings extends Relation
{
    /** @var Person */
    protected $parent;

    public string $sideKey;

    public string $partnerKey;

    public function __construct(
        Person $parent,
        public Sex $side,
    ) {
        [$this->sideKey, $this->partnerKey] = match ($side) {
            Sex::Male => ['father_id', 'mother_id'],
            Sex::Female => ['mother_id', 'father_id'],
        };

        parent::__construct(Person::query()->orderBy('birth_date_from'), $parent);
    }

    public function addConstraints(): void
    {
        if (static::$constraints) {
            $this->query
                ->where($this->sideKey, $this->parent->{$this->sideKey})
                ->where(function (Builder $query) {
                    $query->whereNull($this->partnerKey)
                        ->orWhere($this->partnerKey, '!=', $this->parent->{$this->partnerKey});
                })
                ->whereKeyNot($this->parent->id);
        }
    }

    /**
     * @param list<Person> $people
     */
    public function addEagerConstraints(array $people): void
    {
        $people = collect($people)->filter(function (Person $person) {
            return $person->{$this->sideKey};
        });

        $this->query->whereIn(
            $this->sideKey,
            $people->pluck($this->sideKey)->filter(),
        );
    }

    /**
     * @param list<Person> $people
     *
     * @return list<Person>
     */
    public function initRelation(array $people, $relation): array
    {
        foreach ($people as $person) {
            $person->setRelation($relation, $this->related->newCollection());
        }

        return $people;
    }

    /**
     * @param list<Person> $people
     * @param Collection<int, Person> $siblings
     *
     * @return list<Person>
     */
    public function match(array $people, Collection $siblings, $relation): array
    {
        if ($siblings->isEmpty()) {
            return $people;
        }

        foreach ($people as $person) {
            if ($person->{$this->sideKey} === null) {
                $person->setRelation($relation, $this->related->newCollection());

                continue;
            }

            $person->setRelation(
                $relation,
                $siblings->filter(function (Person $sibling) use ($person) {
                    return $sibling->{$this->sideKey} === $person->{$this->sideKey}
                        && (
                            $sibling->{$this->partnerKey} !== $person->{$this->partnerKey}
                            || $sibling->{$this->partnerKey} === null
                        )
                        && $sibling->id !== $person->id;
                }),
            );
        }

        return $people;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getResults(): Collection
    {
        return $this->parent->{$this->sideKey} !== null
            ? $this->query->get()
            : $this->related->newCollection();
    }
}
