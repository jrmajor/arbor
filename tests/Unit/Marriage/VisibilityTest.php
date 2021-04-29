<?php

use App\Models\Marriage;

it('can determine its visibility', function () {
    $marriage = Marriage::factory()->create();
    expect($marriage->isVisible())->toBeFalse();

    $marriage->woman->visibility = true;
    expect($marriage->isVisible())->toBeFalse();

    $marriage->man->visibility = true;
    expect($marriage->isVisible())->toBeTrue();
});
