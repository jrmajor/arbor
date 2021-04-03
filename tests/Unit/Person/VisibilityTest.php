<?php

use App\Models\Person;

it('can determine its visibility', function () {
    $alive = Person::factory()->alive()->create();
    $dead = Person::factory()->dead()->create();

    $alive->changeVisibility(false);
    expect($alive->isVisible())->toBeFalse();
    $alive->changeVisibility(true);
    expect($alive->isVisible())->toBeTrue();

    $dead->changeVisibility(false);
    expect($dead->isVisible())->toBeFalse();
    $dead->changeVisibility(true);
    expect($dead->isVisible())->toBeTrue();
});

test('change visibility method works', function () {
    $person = Person::factory()->create();

    expect($person->isVisible())->toBeFalse();

    $person->changeVisibility(true);
    expect($person->isVisible())->toBeTrue();

    $person->changeVisibility(false);
    expect($person->isVisible())->toBeFalse();
});
