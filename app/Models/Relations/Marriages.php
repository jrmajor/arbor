<?php

namespace App\Models\Relations;

use App\Models\Marriage;
use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends Relation<Person>
 */
class Marriages extends Relation
{
    /** @var Builder<Marriage> */
    protected $query;

    /** @var Person */
    protected $parent;

    public function __construct(Person $parent)
    {
        $query = Marriage::query()->orderBy(
            $parent->sex === 'xx' ? 'woman_order' : 'man_order',
        );

        parent::__construct($query, $parent);
    }

    public function addConstraints(): void
    {
        if (static::$constraints) {
            $this->query
                ->where('woman_id', $this->parent->id)
                ->orWhere('man_id', $this->parent->id);
        }
    }

    public function addEagerConstraints(array $people): void
    {
        $people = collect($people)->pluck('id');

        $this->query
            ->whereIn('woman_id', $people)
            ->orWhereIn('man_id', $people);
    }

    public function initRelation(array $people, $relation): array
    {
        foreach ($people as $person) {
            $person->setRelation($relation, $this->related->newCollection());
        }

        return $people;
    }

    /**
     * @param Collection<Marriage> $marriages
     */
    public function match(array $people, Collection $marriages, $relation): array
    {
        if ($marriages->isEmpty()) {
            return $people;
        }

        foreach ($people as $person) {
            $person->setRelation(
                $relation,
                $marriages->filter(function (Marriage $marriage) use ($person) {
                    return $marriage->woman_id === $person->id
                        || $marriage->man_id === $person->id;
                }),
            );
        }

        return $people;
    }

    /**
     * @return Collection<Marriage>
     */
    public function getResults(): Collection
    {
        return $this->query->get();
    }
}
