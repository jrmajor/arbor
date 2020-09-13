<?php

use App\Models\Marriage;
use App\Models\User;

it('can determine its visibility', function () {
    $marriage = Marriage::factory()->create();

    expect($marriage->isVisible())->toBeFalse();
    $marriage->woman->changeVisibility(true);
    expect($marriage->isVisible())->toBeFalse();
    $marriage->man->changeVisibility(true);
    expect($marriage->isVisible())->toBeTrue();
});

it('tells if can be viewed by given user', function () {
    $user = User::factory()->create();

    $hiddenMarriage = Marriage::factory()->create();

    $visibleMarriage = Marriage::factory()->create();
    $visibleMarriage->woman->changeVisibility(true);
    $visibleMarriage->man->changeVisibility(true);

    expect($hiddenMarriage->canBeViewedBy($user))->toBeFalse();
    expect($visibleMarriage->canBeViewedBy($user))->toBeTrue();

    $user->permissions = 1;

    expect($hiddenMarriage->canBeViewedBy($user))->toBeTrue();
    expect($visibleMarriage->canBeViewedBy($user))->toBeTrue();
});

it('tells if can be viewed by guest', function () {
    $marriage = Marriage::factory()->create();

    expect($marriage->canBeViewedBy(null))->toBeFalse();

    $marriage->woman->changeVisibility(true);
    $marriage->man->changeVisibility(true);

    expect($marriage->canBeViewedBy(null))->toBeTrue();
});
