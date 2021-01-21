<?php

namespace App\Models\Relations;

use App\Models\Marriage;
use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @method $this orderBy($column, $direction = 'asc')
 */
class Marriages extends Relation
{
    /** @var Person */
    protected $parent;

    public function __construct(Person $parent)
    {
        parent::__construct(Marriage::query(), $parent);
    }

    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query
                ->where('woman_id', $this->parent->id)
                ->orWhere('man_id', $this->parent->id);
        }
    }

    public function addEagerConstraints(array $people)
    {
        $people = collect($people)->pluck('id');

        $this->query
            ->whereIn('woman_id', $people)
            ->orWhereIn('man_id', $people);
    }

    public function initRelation(array $people, $relation)
    {
        foreach ($people as $person) {
            $person->setRelation(
                $relation,
                $this->related->newCollection(),
            );
        }

        return $people;
    }

    public function match(array $people, Collection $marriages, $relation)
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

    public function getResults()
    {
        return $this->query->get();
    }
}
