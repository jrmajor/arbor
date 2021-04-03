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
