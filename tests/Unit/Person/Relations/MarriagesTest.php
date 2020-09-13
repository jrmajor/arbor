<?php

use App\Models\Marriage;
use App\Models\Person;

it('can get marriages', function () {
    $person = Person::factory()->woman()->create();

    Person::factory()->count(3)->man()->create()
        ->each(function ($partner) use ($person) {
            Marriage::factory()->create([
                'woman_id' => $person->id,
                'man_id' => $partner->id,
            ]);
        });

    expect($person->marriages)->toHaveCount(3);
});

it('can eagerly get marriages', function () {
    $woman = Person::factory()->woman()->create();

    Person::factory()->count(3)->man()->create()
        ->each(function ($partner) use ($woman) {
            Marriage::factory()->create([
                'woman_id' => $woman->id,
                'man_id' => $partner->id,
            ]);
        });

    $man = Person::factory()->man()->create();

    Person::factory()->count(4)->woman()->create()
        ->each(function ($partner) use ($man) {
            Marriage::factory()->create([
                'woman_id' => $partner->id,
                'man_id' => $man->id,
            ]);
        });

    $people = Person::whereIn('id', [$woman->id, $man->id])
        ->with('children')->get();

    expect($people->get(0)->marriages)->toHaveCount(3);

    expect($people->get(1)->marriages)->toHaveCount(4);
});
