<?php

use App\Models\Person;

it('can get children', function () {
    $father = Person::factory()->man()->create();

    Person::factory()->count(2)->woman()->create()
        ->each(function ($mother) use ($father) {
            Person::factory()->create([
                'mother_id' => $mother->id,
                'father_id' => $father->id,
            ]);
        });

    $child = Person::factory()->create([
        'mother_id' => null,
        'father_id' => $father->id,
    ]);

    expect($father->children)->toHaveCount(3)
        ->and($father->children->contains($child))->toBeTrue();
});

it('can eagerly get children', function () {
    $mother = Person::factory()->man()->create();

    Person::factory()->count(3)->create([
        'mother_id' => $mother->id,
        'father_id' => null,
    ]);

    Person::factory()->count(2)->create([
        'mother_id' => $mother->id,
        'father_id' => Person::factory()->man()->create(),
    ]);

    $father = Person::factory()->man()->create();

    Person::factory()->count(2)->woman()->create()
        ->each(function ($mother) use ($father) {
            Person::factory()->create([
                'mother_id' => $mother->id,
                'father_id' => $father->id,
            ]);
        });

    $child = Person::factory()->create([
        'mother_id' => null,
        'father_id' => $father->id,
    ]);

    $people = Person::whereIn('id', [$mother->id, $father->id])
        ->with('children')->get();

    expect($people->get(0)->children)->toHaveCount(5);

    expect($people->get(1)->children)->toHaveCount(3)
        ->and($people->get(1)->children->contains($child))->toBeTrue();
});
