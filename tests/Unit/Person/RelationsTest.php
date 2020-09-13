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

it('can get partners', function () {
    $person = Person::factory()->woman()->create();

    $spouse = Person::factory()->man()->create(['name' => 'Spouse']);

    Marriage::factory()->create([
        'woman_id' => $person->id,
        'man_id' => $spouse->id,
    ]);

    $spouseWithChild = Person::factory()->man()->create(['name' => 'Spouse With Child']);

    Marriage::factory()->create([
        'woman_id' => $person->id,
        'man_id' => $spouseWithChild->id,
    ]);

    Person::factory()->create([
        'mother_id' => $person->id,
        'father_id' => $spouseWithChild->id,
    ]);

    $lover = Person::factory()->man()->create(['name' => 'Lover']);

    Person::factory()->create([
        'mother_id' => $person->id,
        'father_id' => $lover->id,
    ]);

    expect($person->partners())->toHaveCount(3);

    expect($person->partners()->contains($spouse))->toBeTrue();
    expect($person->partners()->contains($spouseWithChild))->toBeTrue();
    expect($person->partners()->contains($lover))->toBeTrue();
})->skip();

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

    expect($father->children)->toHaveCount(3);
    expect($father->children->contains($child))->toBeTrue();
});
