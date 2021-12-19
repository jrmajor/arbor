<?php

use App\Models\Marriage;
use App\Models\Person;
use Illuminate\Database\Eloquent\Model;

it('can get marriages', function () {
    $person = Person::factory()->female()->create();

    Marriage::factory(3)->create(['woman_id' => $person]);

    expect($person->marriages)->toHaveCount(3);
});

it('can eagerly get marriages', function () {
    Model::preventLazyLoading(false);

    $woman = Person::factory()->female()->create();
    $man = Person::factory()->male()->create();

    Marriage::factory(3)->create(['woman_id' => $woman]);
    Marriage::factory(4)->create(['man_id' => $man]);

    $people = Person::query()
        ->whereIn('id', [$woman->id, $man->id])
        ->with('children')->get();

    expect($people)->sequence(
        fn ($e) => $e->marriages->toHaveCount(3),
        fn ($e) => $e->marriages->toHaveCount(4),
    );
});
