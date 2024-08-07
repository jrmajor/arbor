<?php

namespace App\Models\Relations;

use App\Enums\Sex;
use App\Models\Marriage;
use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends Relation<Marriage, Person, Collection<int, Marriage>>
 */
class Marriages extends Relation
{
    /** @var Builder<Marriage> */
    protected $query;

    /** @var Person */
    protected $parent;

    public function __construct(Person $parent)
    {
        $query = Marriage::query()->orderBy(match ($parent->sex) {
            Sex::Male => 'man_order',
            Sex::Female => 'woman_order',
            null => 'first_event_date_from',
        });

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

    /**
     * @param list<Person> $people
     */
    public function addEagerConstraints(array $people): void
    {
        $people = collect($people)->pluck('id');

        $this->query
            ->whereIn('woman_id', $people)
            ->orWhereIn('man_id', $people);
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
     * @param Collection<int, Marriage> $marriages
     *
     * @return list<Person>
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
     * @return Collection<int, Marriage>
     */
    public function getResults(): Collection
    {
        return $this->query->get();
    }
}
