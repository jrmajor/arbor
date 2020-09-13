<?php

namespace App\Models\Relations;

use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

class HalfSiblings extends Relation
{
    public function __construct(Person $parent, $side)
    {
        $this->side = $side;
        $this->sideKey = $side.'_id';
        $this->partnerKey = $side == 'mother' ? 'father_id' : 'mother_id';

        parent::__construct(Person::query(), $parent);
    }

    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query
                ->where($this->sideKey, $this->parent->{$this->sideKey})
                ->where(function ($query) {
                    $query->whereNull($this->partnerKey)
                        ->orWhere($this->partnerKey, '!=', $this->parent->{$this->partnerKey});
                })->where('id', '!=', $this->parent->id);
        }
    }

    public function addEagerConstraints(array $people)
    {
        $people = collect($people)->filter(function (Person $person) {
            return $person->{$this->sideKey};
        });

        $this->query->whereIn(
            $this->sideKey,
            $people->pluck($this->sideKey)->filter()
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
            if ($person->{$this->sideKey} === null) {
                return $person->setRelation(
                    $relation, $this->related->newCollection()
                );
            }

            $person->setRelation(
                $relation,
                $siblings->filter(function (Person $sibling) use ($person) {
                    return $sibling->{$this->sideKey} === $person->{$this->sideKey}
                        && ($sibling->{$this->partnerKey} !== $person->{$this->partnerKey}
                            || $sibling->{$this->partnerKey} === null)
                        && $sibling->id !== $person->id;
                })
            );
        }

        return $people;
    }

    public function getResults()
    {
        return ! is_null($this->parent->{$this->sideKey})
                ? $this->query->get()
                : $this->related->newCollection();
    }
}
