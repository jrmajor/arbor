<?php

use App\Models\Person;

it('can get siblings and half siblings', function () {
    $mother = Person::factory()->woman()->create();
    $father = Person::factory()->man()->create();

    $person = Person::factory()->create([
        'mother_id' => $mother->id,
        'father_id' => $father->id,
    ]);

    Person::factory()->count(2)->create([
        'mother_id' => $person->mother_id,
        'father_id' => $person->father_id,
    ]);

    Person::factory()->count(1)->create([
        'mother_id' => $person->mother_id,
        'father_id' => Person::factory()->man()->create(),
    ]);
    Person::factory()->count(2)->create([
        'mother_id' => $person->mother_id,
        'father_id' => null,
    ]);

    Person::factory()->count(3)->create([
        'mother_id' => Person::factory()->woman()->create(),
        'father_id' => $person->father_id,
    ]);
    Person::factory()->count(1)->create([
        'mother_id' => null,
        'father_id' => $person->father_id,
    ]);

    expect($person->siblings)->toHaveCount(2);

    expect($person->siblings_mother)->toHaveCount(3);

    expect($person->siblings_father)->toHaveCount(4);

    $person->mother_id = null;
    tap($person)->save()->refresh();

    expect($person->siblings)->toBeEmpty();

    expect($person->siblings_mother)->toBeEmpty();

    expect($person->siblings_father)->toHaveCount(6);
});

it('can eagerly get siblings and half siblings', function () {
    $firstMother = Person::factory()->woman()->create();
    $firstFather = Person::factory()->man()->create();

    $firstPerson = Person::factory()->create([
        'mother_id' => $firstMother->id,
        'father_id' => $firstFather->id,
    ]);

    Person::factory()->count(2)->create([
        'mother_id' => $firstPerson->mother_id,
        'father_id' => $firstPerson->father_id,
    ]);

    Person::factory()->count(1)->create([
        'mother_id' => $firstPerson->mother_id,
        'father_id' => Person::factory()->man()->create(),
    ]);
    Person::factory()->count(2)->create([
        'mother_id' => $firstPerson->mother_id,
        'father_id' => null,
    ]);

    Person::factory()->count(3)->create([
        'mother_id' => Person::factory()->woman()->create(),
        'father_id' => $firstPerson->father_id,
    ]);
    Person::factory()->count(1)->create([
        'mother_id' => null,
        'father_id' => $firstPerson->father_id,
    ]);

    $secondMother = Person::factory()->woman()->create();
    $secondFather = Person::factory()->man()->create();

    $secondPerson = Person::factory()->create([
        'mother_id' => $secondMother->id,
        'father_id' => $secondFather->id,
    ]);

    Person::factory()->count(3)->create([
        'mother_id' => $secondPerson->mother_id,
        'father_id' => $secondPerson->father_id,
    ]);

    Person::factory()->count(3)->create([
        'mother_id' => $secondPerson->mother_id,
        'father_id' => Person::factory()->man()->create(),
    ]);
    Person::factory()->count(2)->create([
        'mother_id' => $secondPerson->mother_id,
        'father_id' => null,
    ]);

    Person::factory()->count(2)->create([
        'mother_id' => Person::factory()->woman()->create(),
        'father_id' => $secondPerson->father_id,
    ]);
    Person::factory()->count(4)->create([
        'mother_id' => null,
        'father_id' => $secondPerson->father_id,
    ]);

    $people = Person::whereIn('id', [$firstPerson->id, $secondPerson->id])
        ->with('siblings', 'siblings_mother', 'siblings_father')->get();

    expect($people->get(0)->siblings)->toHaveCount(2);

    expect($people->get(1)->siblings)->toHaveCount(3);

    expect($people->get(0)->siblings_mother)->toHaveCount(3);

    expect($people->get(1)->siblings_mother)->toHaveCount(5);

    expect($people->get(0)->siblings_father)->toHaveCount(4);

    expect($people->get(1)->siblings_father)->toHaveCount(6);

    $firstPerson->mother_id = null;
    $firstPerson->save();

    $secondPerson->mother_id = null;
    $secondPerson->father_id = null;
    $secondPerson->save();

    $people = Person::whereIn('id', [$firstPerson->id, $secondPerson->id])
        ->with('siblings')->get();

    expect($people->get(0)->siblings)->toBeEmpty();

    expect($people->get(1)->siblings)->toBeEmpty();

    expect($people->get(0)->siblings_mother)->toBeEmpty();

    expect($people->get(1)->siblings_mother)->toBeEmpty();

    expect($people->get(0)->siblings_father)->toHaveCount(6);

    expect($people->get(1)->siblings_father)->toBeEmpty();
});
