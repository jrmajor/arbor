<?php

use App\Enums\MarriageEventTypeEnum;
use App\Enums\MarriageRiteEnum;
use App\Models\Marriage;
use Spatie\Enum\Phpunit\EnumAssertions;

uses(EnumAssertions::class);

it('casts rite to enum', function () {
    $marriage = Marriage::factory()->create([
        'rite' => 'roman_catholic',
    ]);

    $this->assertSameEnum(MarriageRiteEnum::roman_catholic(), $marriage->rite);
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

    $this->assertSameEnum(MarriageEventTypeEnum::civil_marriage(), $marriage->first_event_type);

    $this->assertSameEnum(MarriageEventTypeEnum::church_marriage(), $marriage->second_event_type);
});

test('events types are nullable', function () {
    $marriage = Marriage::factory()->create([
        'first_event_type' => null,
        'second_event_type' => null,
    ]);

    expect($marriage->first_event_type)->toBeNull();
    expect($marriage->second_event_type)->toBeNull();
});
