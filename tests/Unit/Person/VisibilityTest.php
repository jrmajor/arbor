<?php

use App\Models\Person;

it('can determine its visibility', function () {
    $alive = Person::factory()->alive()->create();
    $dead = Person::factory()->dead()->create();

    $alive->visibility = false;
    expect($alive->isVisible())->toBeFalse();
    $alive->visibility = true;
    expect($alive->isVisible())->toBeTrue();

    $dead->visibility = false;
    expect($dead->isVisible())->toBeFalse();
    $dead->visibility = true;
    expect($dead->isVisible())->toBeTrue();
});

test('visibility can be updated', function () {
    $person = Person::factory()->create([
        'visibility' => false,
    ]);

    $person->fill(['visibility' => true])->save();
    expect($person->visibility)->toBeFalse();

    $person->forceFill(['visibility' => true])->save();
    expect($person->visibility)->toBeTrue();
});

test("visibility can't be updated with other attributes", function () {
    $person = Person::factory()->create([
        'name' => 'Old Name',
        'visibility' => false,
    ]);

    $person->forceFill([
        'name' => 'New Name',
        'visibility' => true,
    ])->save();
})->throws(Exception::class);
