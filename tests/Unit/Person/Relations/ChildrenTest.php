<?php

use App\Models\Person;

it('can get children', function () {
    $father = Person::factory()->man()->create();

    Person::factory(2)
        ->withParents()
        ->create(['father_id' => $father]);

    Person::factory()
        ->withoutParents()
        ->create(['father_id' => $father]);

    expect($father->children)->toHaveCount(3);
});

it('can eagerly get children', function () {
    $mother = Person::factory()->woman()->create();

    Person::factory(3)
        ->withoutParents()
        ->create(['mother_id' => $mother]);

    Person::factory(2)
        ->withParents()
        ->create(['mother_id' => $mother]);

    $father = Person::factory()->man()->create();

    Person::factory()
        ->withoutParents()
        ->create(['father_id' => $father]);

    Person::factory(2)
        ->withParents()
        ->create(['father_id' => $father]);

    [$mother, $father] = Person::query()
        ->whereIn('id', [$mother->id, $father->id])
        ->with('children')->get();

    expect($mother->children)->toHaveCount(5);
    expect($father->children)->toHaveCount(3);
});
