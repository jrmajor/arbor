<?php

use App\Models\Marriage;
use App\Models\Person;

it('can get marriages', function () {
    $person = Person::factory()->woman()->create();

    Marriage::factory(3)->create(['woman_id' => $person]);

    expect($person->marriages)->toHaveCount(3);
});

it('can eagerly get marriages', function () {
    $woman = Person::factory()->woman()->create();
    $man = Person::factory()->man()->create();

    Marriage::factory(3)->create(['woman_id' => $woman]);
    Marriage::factory(4)->create(['man_id' => $man]);

    $people = Person::whereIn('id', [$woman->id, $man->id])
        ->with('children')->get();

    expect($people->get(0)->marriages)->toHaveCount(3);

    expect($people->get(1)->marriages)->toHaveCount(4);
});
