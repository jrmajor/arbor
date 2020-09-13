<?php

namespace Tests\Unit;

use App\Enums\MarriageEventTypeEnum;
use App\Enums\MarriageRiteEnum;
use App\Models\Marriage;
use App\Models\Person;
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

it('can get man and woman', function () {
    $woman = Person::factory()->woman()->create();
    $man = Person::factory()->man()->create();
    $marriage = Marriage::factory()->create([
        'woman_id' => $woman->id,
        'man_id' => $man->id,
    ]);

    expect($woman->is($marriage->woman))->toBeTrue();
    expect($man->is($marriage->man))->toBeTrue();
});

it('can get partner', function () {
    $woman = Person::factory()->woman()->create();
    $man = Person::factory()->man()->create();
    $marriage = Marriage::factory()->create([
        'woman_id' => $woman->id,
        'man_id' => $man->id,
    ]);

    expect($woman->is($marriage->partner($man)))->toBeTrue();
    expect($man->is($marriage->partner($woman)))->toBeTrue();
});

it('can get order in given person marriages', function () {
    $woman = Person::factory()->woman()->create();
    $man = Person::factory()->man()->create();

    $first_marriage = Marriage::factory()->create([
        'woman_id' => $woman->id,
        'woman_order' => null,
        'man_id' => $man->id,
        'man_order' => 1,
    ]);
    $second_marriage = Marriage::factory()->create([
        'man_id' => $man->id,
        'man_order' => 2,
    ]);

    expect($first_marriage->order($woman))->toBeNull();
    expect($first_marriage->order($man))->toBe(1);
    expect($second_marriage->order($man))->toBe(2);
});

it('can determine if has events', function () {
    $first_marriage = Marriage::factory()->create([
        'first_event_type' => 'civil_marriage',
        'first_event_date_from' => '2014-06-23',
        'first_event_date_to' => '2014-06-23',
        'first_event_place' => 'Żnin, Polska',
        'second_event_type' => null,
        'second_event_date_from' => null,
        'second_event_date_to' => null,
        'second_event_place' => null,
    ]);

    expect($first_marriage->hasFirstEvent())->toBeTrue();
    expect($first_marriage->hasSecondEvent())->toBeFalse();

    $second_marriage = Marriage::factory()->create([
        'first_event_type' => 'concordat_marriage',
        'first_event_date_from' => null,
        'first_event_date_to' => null,
        'first_event_place' => null,
        'second_event_type' => null,
        'second_event_date_from' => '1863-01-31',
        'second_event_date_to' => '1863-01-31',
        'second_event_place' => null,
    ]);

    expect($second_marriage->hasFirstEvent())->toBeTrue();
    expect($second_marriage->hasSecondEvent())->toBeTrue();

    $third_marriage = Marriage::factory()->create([
        'first_event_type' => null,
        'first_event_date_from' => null,
        'first_event_date_to' => null,
        'first_event_place' => 'Lwów, Litwa',
        'second_event_type' => 'civil_marriage',
        'second_event_date_from' => '1863-01-31',
        'second_event_date_to' => '1863-01-31',
        'second_event_place' => null,
    ]);

    expect($third_marriage->hasFirstEvent())->toBeTrue();
    expect($third_marriage->hasSecondEvent())->toBeTrue();
});
