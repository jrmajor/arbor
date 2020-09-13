<?php

namespace App\Models\Relations;

use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

class Siblings extends Relation
{
    public function __construct(Person $parent)
    {
        parent::__construct(Person::query(), $parent);
    }

    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query
                ->where('mother_id', $this->parent->mother_id)
                ->where('father_id', $this->parent->father_id)
                ->where('id', '!=', $this->parent->id);
        }
    }

    public function addEagerConstraints(array $people)
    {
        $people = collect($people)->filter(function (Person $person) {
            return $person->mother_id && $person->father_id;
        });

        $this->query->whereIn(
            'mother_id',
            $people->pluck('mother_id')
        )->whereIn(
            'father_id',
            $people->pluck('father_id')
        );
    }

    public function initRelation(array $people, $relation)
    {
        foreach ($people as $person) {
            $person->setRelation(
                $relation,
                $this->related->newCollection()
            );
        }

        return $people;
    }

    public function match(array $people, Collection $siblings, $relation)
    {
        if ($siblings->isEmpty()) {
            return $people;
        }

        foreach ($people as $person) {
            $person->setRelation(
                $relation,
                $siblings->filter(function (Person $sibling) use ($person) {
                    return $sibling->mother_id === $person->mother_id
                        && $sibling->father_id === $person->father_id
                        && $sibling->id !== $person->id;
                })
            );
        }

        return $people;
    }

    public function getResults()
    {
        return ! is_null($this->parent->mother_id)
            && ! is_null($this->parent->father_id)
                ? $this->query->get()
                : $this->related->newCollection();
    }
}
