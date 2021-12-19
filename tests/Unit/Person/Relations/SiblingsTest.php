<?php

use App\Models\Person;
use Illuminate\Database\Eloquent\Model;

it('can get siblings and half siblings', function () {
    [$person] = Person::factory(3)->create([
        'mother_id' => $mother = Person::factory()->female()->create(),
        'father_id' => $father = Person::factory()->male()->create(),
    ]);

    assert($person instanceof Person);

    Person::factory()
        ->withParents()
        ->create(['mother_id' => $mother]);

    Person::factory(2)
        ->withoutParents()
        ->create(['mother_id' => $mother]);

    Person::factory(3)
        ->withParents()
        ->create(['father_id' => $father]);

    Person::factory()
        ->withoutParents()
        ->create(['father_id' => $father]);

    expect($person)
        ->siblings->toHaveCount(2)
        ->siblings_mother->toHaveCount(3)
        ->siblings_father->toHaveCount(4);

    $person->mother_id = null;
    tap($person)->save()->refresh();

    expect($person)
        ->siblings->toBeEmpty()
        ->siblings_mother->toBeEmpty()
        ->siblings_father->toHaveCount(6);
});

it('can eagerly get siblings and half siblings', function () {
    Model::preventLazyLoading(false);

    [$firstPerson] = Person::factory(3)->create([
        'mother_id' => $firstMother = Person::factory()->female()->create(),
        'father_id' => $firstFather = Person::factory()->male()->create(),
    ]);

    Person::factory()
        ->withParents()
        ->create(['mother_id' => $firstMother]);

    Person::factory(2)
        ->withoutParents()
        ->create(['mother_id' => $firstMother]);

    Person::factory(3)
        ->withParents()
        ->create(['father_id' => $firstFather]);

    Person::factory()
        ->withoutParents()
        ->create(['father_id' => $firstFather]);

    [$secondPerson] = Person::factory(4)->create([
        'mother_id' => $secondMother = Person::factory()->female()->create(),
        'father_id' => $secondFather = Person::factory()->male()->create(),
    ]);

    Person::factory(3)
        ->withParents()
        ->create(['mother_id' => $secondMother]);

    Person::factory(2)
        ->withoutParents()
        ->create(['mother_id' => $secondMother]);

    Person::factory(2)
        ->withParents()
        ->create(['father_id' => $secondFather]);

    Person::factory(4)
        ->withoutParents()
        ->create(['father_id' => $secondFather]);

    $people = Person::whereIn('id', [$firstPerson->id, $secondPerson->id])
        ->with('siblings', 'siblings_mother', 'siblings_father')->get();

    expect($people[0])
        ->siblings->toHaveCount(2)
        ->siblings_mother->toHaveCount(3)
        ->siblings_father->toHaveCount(4);

    expect($people[1])
        ->siblings->toHaveCount(3)
        ->siblings_mother->toHaveCount(5)
        ->siblings_father->toHaveCount(6);

    $firstPerson->update(['mother_id' => null]);

    $secondPerson->update([
        'mother_id' => null,
        'father_id' => null,
    ]);

    $people = Person::query()
        ->whereIn('id', [$firstPerson->id, $secondPerson->id])
        ->with('siblings')->get();

    expect($people[0])
        ->siblings->toBeEmpty()
        ->siblings_mother->toBeEmpty()
        ->siblings_father->toHaveCount(6);

    expect($people[1])
        ->siblings->toBeEmpty()
        ->siblings_mother->toBeEmpty()
        ->siblings_father->toBeEmpty();
});
