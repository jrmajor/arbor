<?php

use App\Models\Marriage;
use App\Models\Person;

it('can get mother', function () {
    $mother = Person::factory()->woman()->create();
    $person = Person::factory()->create(['mother_id' => $mother->id]);

    expect($person->mother->id)->toBe($mother->id);
});

it('can get father', function () {
    $father = Person::factory()->man()->create();
    $person = Person::factory()->create(['father_id' => $father->id]);

    expect($person->father->id)->toBe($father->id);
});
