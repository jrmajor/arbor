<?php

use App\Models\Person;
use App\Models\User;

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

it('tells if it can be viewed by given user', function () {
    $user = User::factory()->create();

    $hiddenPerson = Person::factory()->create([
        'visibility' => false,
    ]);

    $visiblePerson = Person::factory()->create([
        'visibility' => true,
    ]);

    expect($hiddenPerson->canBeViewedBy($user))->toBeFalse();
    expect($visiblePerson->canBeViewedBy($user))->toBeTrue();

    $user->permissions = 1;

    expect($hiddenPerson->canBeViewedBy($user))->toBeTrue();
    expect($visiblePerson->canBeViewedBy($user))->toBeTrue();
});

it('tells if can be viewed by guest', function () {
    $person = Person::factory()->create();

    expect($person->canBeViewedBy(null))->toBeFalse();

    $person->changeVisibility(true);

    expect($person->canBeViewedBy(null))->toBeTrue();
});
