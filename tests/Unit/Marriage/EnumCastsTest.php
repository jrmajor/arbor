<?php

use App\Enums\MarriageEventType;
use App\Enums\MarriageRite;
use App\Models\Marriage;

it('casts rite to enum', function () {
    $marriage = Marriage::factory()->create([
        'rite' => 'roman_catholic',
    ]);

    expect($marriage->rite)->toBe(MarriageRite::RomanCatholic);
});

test('rite is nullable', function () {
    $marriage = Marriage::factory()->create([
        'rite' => null,
    ]);

    expect($marriage->rite)->toBeNull();
});

it('casts events types to enums', function () {
    $marriage = Marriage::factory()->create([
        'first_event_type' => 'civil_marriage',
        'second_event_type' => 'church_marriage',
    ]);

    expect($marriage->first_event_type)->toBe(MarriageEventType::CivilMarriage);

    expect($marriage->second_event_type)->toBe(MarriageEventType::ChurchMarriage);
});

test('events types are nullable', function () {
    $marriage = Marriage::factory()->create([
        'first_event_type' => null,
        'second_event_type' => null,
    ]);

    expect($marriage->first_event_type)->toBeNull();
    expect($marriage->second_event_type)->toBeNull();
});
