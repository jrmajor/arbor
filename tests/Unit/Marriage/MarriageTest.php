<?php

namespace Tests\Unit;

use App\Models\Marriage;
use App\Models\Person;

it('can get man and woman', function () {
    $marriage = Marriage::factory()->create([
        'woman_id' => $woman = Person::factory()->woman()->create(),
        'man_id' => $man = Person::factory()->man()->create(),
    ]);

    expect($marriage->woman())->toBeModel($woman);
    expect($marriage->man())->toBeModel($man);
});

it('can get partner', function () {
    $marriage = Marriage::factory()->create([
        'woman_id' => $woman = Person::factory()->woman()->create(),
        'man_id' => $man = Person::factory()->man()->create(),
    ]);

    expect($marriage->partner($man))->toBeModel($woman);
    expect($marriage->partner($woman))->toBeModel($man);
});

it('can get order in given person marriages', function () {
    $woman = Person::factory()->woman()->create();
    $man = Person::factory()->man()->create();

    $marriages = Marriage::factory(2)
        ->sequence([
            'woman_id' => $woman->id,
            'woman_order' => null,
            'man_id' => $man->id,
            'man_order' => 1,
        ], [
            'man_id' => $man->id,
            'man_order' => 2,
        ])
        ->create();

    [$firstMarriage, $secondMarriage] = $marriages;

    expect($firstMarriage->order($woman))->toBeNull();
    expect($firstMarriage->order($man))->toBe(1);
    expect($secondMarriage->order($man))->toBe(2);
});

it('can determine if has events', function () {
    $marriages = Marriage::factory(3)
        ->sequence([
            'first_event_type' => 'civil_marriage',
            'first_event_date_from' => '2014-06-23',
            'first_event_date_to' => '2014-06-23',
            'first_event_place' => 'Żnin, Polska',
            'second_event_type' => null,
            'second_event_date_from' => null,
            'second_event_date_to' => null,
            'second_event_place' => null,
        ], [
            'first_event_type' => 'concordat_marriage',
            'first_event_date_from' => null,
            'first_event_date_to' => null,
            'first_event_place' => null,
            'second_event_type' => null,
            'second_event_date_from' => '1863-01-31',
            'second_event_date_to' => '1863-01-31',
            'second_event_place' => null,
        ], [
            'first_event_type' => null,
            'first_event_date_from' => null,
            'first_event_date_to' => null,
            'first_event_place' => 'Lwów, Litwa',
            'second_event_type' => 'civil_marriage',
            'second_event_date_from' => '1863-01-31',
            'second_event_date_to' => '1863-01-31',
            'second_event_place' => null,
        ])
        ->create();

    [$firstMarriage, $secondMarriage, $thirdMarriage] = $marriages;

    expect($firstMarriage->hasFirstEvent())->toBeTrue();
    expect($firstMarriage->hasSecondEvent())->toBeFalse();

    expect($secondMarriage->hasFirstEvent())->toBeTrue();
    expect($secondMarriage->hasSecondEvent())->toBeTrue();

    expect($thirdMarriage->hasFirstEvent())->toBeTrue();
    expect($thirdMarriage->hasSecondEvent())->toBeTrue();
});
