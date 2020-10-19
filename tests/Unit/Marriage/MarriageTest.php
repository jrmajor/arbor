<?php

namespace Tests\Unit;

use App\Models\Marriage;
use App\Models\Person;

it('can get man and woman', function () {
    $woman = Person::factory()->woman()->create();
    $man = Person::factory()->man()->create();
    $marriage = Marriage::factory()->create([
        'woman_id' => $woman->id,
        'man_id' => $man->id,
    ]);

    expect($marriage->woman()->is($woman))->toBeTrue();
    expect($marriage->man()->is($man))->toBeTrue();
});

it('can get partner', function () {
    $woman = Person::factory()->woman()->create();
    $man = Person::factory()->man()->create();
    $marriage = Marriage::factory()->create([
        'woman_id' => $woman->id,
        'man_id' => $man->id,
    ]);

    expect($marriage->partner($man)->is($woman))->toBeTrue();
    expect($marriage->partner($woman)->is($man))->toBeTrue();
});

it('can get order in given person marriages', function () {
    $woman = Person::factory()->woman()->create();
    $man = Person::factory()->man()->create();

    $firstMarriage = Marriage::factory()->create([
        'woman_id' => $woman->id,
        'woman_order' => null,
        'man_id' => $man->id,
        'man_order' => 1,
    ]);
    $secondMarriage = Marriage::factory()->create([
        'man_id' => $man->id,
        'man_order' => 2,
    ]);

    expect($firstMarriage->order($woman))->toBeNull();
    expect($firstMarriage->order($man))->toBe(1);
    expect($secondMarriage->order($man))->toBe(2);
});

it('can determine if has events', function () {
    $firstMarriage = Marriage::factory()->create([
        'first_event_type' => 'civil_marriage',
        'first_event_date_from' => '2014-06-23',
        'first_event_date_to' => '2014-06-23',
        'first_event_place' => 'Żnin, Polska',
        'second_event_type' => null,
        'second_event_date_from' => null,
        'second_event_date_to' => null,
        'second_event_place' => null,
    ]);

    expect($firstMarriage->hasFirstEvent())->toBeTrue();
    expect($firstMarriage->hasSecondEvent())->toBeFalse();

    $secondMarriage = Marriage::factory()->create([
        'first_event_type' => 'concordat_marriage',
        'first_event_date_from' => null,
        'first_event_date_to' => null,
        'first_event_place' => null,
        'second_event_type' => null,
        'second_event_date_from' => '1863-01-31',
        'second_event_date_to' => '1863-01-31',
        'second_event_place' => null,
    ]);

    expect($secondMarriage->hasFirstEvent())->toBeTrue();
    expect($secondMarriage->hasSecondEvent())->toBeTrue();

    $thirdMarriage = Marriage::factory()->create([
        'first_event_type' => null,
        'first_event_date_from' => null,
        'first_event_date_to' => null,
        'first_event_place' => 'Lwów, Litwa',
        'second_event_type' => 'civil_marriage',
        'second_event_date_from' => '1863-01-31',
        'second_event_date_to' => '1863-01-31',
        'second_event_place' => null,
    ]);

    expect($thirdMarriage->hasFirstEvent())->toBeTrue();
    expect($thirdMarriage->hasSecondEvent())->toBeTrue();
});
