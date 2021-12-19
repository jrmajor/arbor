<?php

use App\Models\Person;

it('can get mother', function () {
    $mother = Person::factory()->female()->create();
    $person = Person::factory()->create(['mother_id' => $mother]);

    expect($person->mother)->toBeModel($mother);
});

it('can get father', function () {
    $father = Person::factory()->male()->create();
    $person = Person::factory()->create(['father_id' => $father]);

    expect($person->father)->toBeModel($father);
});
