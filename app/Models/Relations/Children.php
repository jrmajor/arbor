<?php

namespace App\Models\Relations;

use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @method $this orderBy($column, $direction = 'asc')
 */
class Children extends Relation
{
    /** @var Person */
    protected $parent;

    public function __construct(Person $parent)
    {
        parent::__construct(Person::query(), $parent);
    }

    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query
                ->where('mother_id', $this->parent->id)
                ->orWhere('father_id', $this->parent->id);
        }
    }

    public function addEagerConstraints(array $people)
    {
        $people = collect($people)->pluck('id');

        $this->query
            ->whereIn('mother_id', $people)
            ->orWhereIn('father_id', $people);
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

    public function match(array $people, Collection $children, $relation)
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

    public function getResults()
    {
        return $this->query->get();
    }
}
