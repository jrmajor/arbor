<?php

use App\Enums\MarriageEventTypeEnum;
use App\Enums\MarriageRiteEnum;
use App\Models\Marriage;

it('casts rite to enum', function () {
    $marriage = Marriage::factory()->create([
        'rite' => 'roman_catholic',
    ]);

    expect($marriage->rite)->toBeInstanceOf(MarriageRiteEnum::class);
    expect($marriage->rite->isEqual(MarriageRiteEnum::roman_catholic()))->toBeTrue();
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

    expect($marriage->first_event_type)->toBeInstanceOf(MarriageEventTypeEnum::class);
    expect($marriage->first_event_type->isEqual(MarriageEventTypeEnum::civil_marriage()))->toBeTrue();

    expect($marriage->second_event_type)->toBeInstanceOf(MarriageEventTypeEnum::class);
    expect($marriage->second_event_type->isEqual(MarriageEventTypeEnum::church_marriage()))->toBeTrue();
});

test('events types are nullable', function () {
    $marriage = Marriage::factory()->create([
        'first_event_type' => null,
        'second_event_type' => null,
    ]);

    expect($marriage->first_event_type)->toBeNull();
    expect($marriage->second_event_type)->toBeNull();
});
