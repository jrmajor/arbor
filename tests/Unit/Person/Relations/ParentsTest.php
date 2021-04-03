<?php

use App\Models\Person;

it('can get mother', function () {
    $mother = Person::factory()->woman()->create();
    $person = Person::factory()->create(['mother_id' => $mother]);

    expect($person->mother)->toBeModel($mother);
});

it('can get father', function () {
    $father = Person::factory()->man()->create();
    $person = Person::factory()->create(['father_id' => $father]);

    expect($person->father)->toBeModel($father);
});
