<?php

namespace App\Models\Relations;

use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends Relation<Person>
 */
class Children extends Relation
{
    /** @var Builder<Person> */
    protected $query;

    /** @var Person */
    protected $parent;

    public function __construct(Person $parent)
    {
        parent::__construct(Person::query()->orderBy('birth_date_from'), $parent);
    }

    public function addConstraints(): void
    {
        if (static::$constraints) {
            $this->query
                ->where('mother_id', $this->parent->id)
                ->orWhere('father_id', $this->parent->id);
        }
    }

    public function addEagerConstraints(array $people): void
    {
        $people = collect($people)->pluck('id');

        $this->query
            ->whereIn('mother_id', $people)
            ->orWhereIn('father_id', $people);
    }

    public function initRelation(array $people, $relation): array
    {
        foreach ($people as $person) {
            $person->setRelation($relation, $this->related->newCollection());
        }

        return $people;
    }

    /**
     * @param Collection<int, Person> $children
     */
    public function match(array $people, Collection $children, $relation): array
    {
        if ($children->isEmpty()) {
            return $people;
        }

        foreach ($people as $person) {
            $person->setRelation(
                $relation,
                $children->filter(function (Person $child) use ($person) {
                    return $child->mother_id === $person->id
                        || $child->father_id === $person->id;
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
        return $this->query->get();
    }
}
