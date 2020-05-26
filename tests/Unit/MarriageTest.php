<?php

namespace Tests\Unit;

use App\Enums\MarriageEventTypeEnum;
use App\Enums\MarriageRiteEnum;
use App\Marriage;
use App\Person;
use App\User;

it('can determine its visibility', function () {
    $marriage = factory(Marriage::class)->create();

    assertFalse($marriage->isVisible());
    $marriage->woman->changeVisibility(true);
    assertFalse($marriage->isVisible());
    $marriage->man->changeVisibility(true);
    assertTrue($marriage->isVisible());
});

it('tells if can be viewed by given user', function () {
    $user = factory(User::class)->create();

    $hiddenMarriage = factory(Marriage::class)->create();

    $visibleMarriage = factory(Marriage::class)->create();
    $visibleMarriage->woman->changeVisibility(true);
    $visibleMarriage->man->changeVisibility(true);

    assertFalse($hiddenMarriage->canBeViewedBy($user));
    assertTrue($visibleMarriage->canBeViewedBy($user));

    $user->permissions = 1;

    assertTrue($hiddenMarriage->canBeViewedBy($user));
    assertTrue($visibleMarriage->canBeViewedBy($user));
});

it('tells if can be viewed by guest', function () {
    $marriage = factory(Marriage::class)->create();

    assertFalse($marriage->canBeViewedBy(null));

    $marriage->woman->changeVisibility(true);
    $marriage->man->changeVisibility(true);

    assertTrue($marriage->canBeViewedBy(null));
});

it('casts rite to enum', function () {
    $marriage = factory(Marriage::class)->create([
        'rite' => 'roman_catholic',
    ]);

    assertInstanceOf(MarriageRiteEnum::class, $marriage->rite);
    assertTrue($marriage->rite->isEqual(MarriageRiteEnum::roman_catholic()));
});

test('rite is nullable', function () {
    $marriage = factory(Marriage::class)->create([
        'rite' => null,
    ]);

    assertNull($marriage->rite);
});

it('casts events types to enums', function () {
    $marriage = factory(Marriage::class)->create([
        'first_event_type' => 'civil_marriage',
        'second_event_type' => 'church_marriage',
    ]);

    assertInstanceOf(MarriageEventTypeEnum::class, $marriage->first_event_type);
    assertTrue($marriage->first_event_type->isEqual(MarriageEventTypeEnum::civil_marriage()));

    assertInstanceOf(MarriageEventTypeEnum::class, $marriage->second_event_type);
    assertTrue($marriage->second_event_type->isEqual(MarriageEventTypeEnum::church_marriage()));
});

test('events types are nullable', function () {
    $marriage = factory(Marriage::class)->create([
        'first_event_type' => null,
        'second_event_type' => null,
    ]);

    assertNull($marriage->first_event_type);
    assertNull($marriage->second_event_type);
});

it('can get man and woman', function () {
    $woman = factory(Person::class)->states('woman')->create();
    $man = factory(Person::class)->states('man')->create();
    $marriage = factory(Marriage::class)->create([
        'woman_id' => $woman->id,
        'man_id' => $man->id,
    ]);

    assertTrue($woman->is($marriage->woman));
    assertTrue($man->is($marriage->man));
});

it('can get partner', function () {
    $woman = factory(Person::class)->states('woman')->create();
    $man = factory(Person::class)->states('man')->create();
    $marriage = factory(Marriage::class)->create([
        'woman_id' => $woman->id,
        'man_id' => $man->id,
    ]);

    assertTrue($woman->is($marriage->partner($man)));
    assertTrue($man->is($marriage->partner($woman)));
});

it('can get order in given person marriages', function () {
    $woman = factory(Person::class)->states('woman')->create();
    $man = factory(Person::class)->states('man')->create();

    $first_marriage = factory(Marriage::class)->create([
        'woman_id' => $woman->id,
        'woman_order' => null,
        'man_id' => $man->id,
        'man_order' => 1,
    ]);
    $second_marriage = factory(Marriage::class)->create([
        'man_id' => $man->id,
        'man_order' => 2,
    ]);

    assertNull($first_marriage->order($woman));
    assertEquals(1, $first_marriage->order($man));
    assertEquals(2, $second_marriage->order($man));
});

it('can determine if has events', function () {
    $first_marriage = factory(Marriage::class)->create([
        'first_event_type' => 'civil_marriage',
        'first_event_date_from' => '2014-06-23',
        'first_event_date_to' => '2014-06-23',
        'first_event_place' => 'Żnin, Polska',
        'second_event_type' => null,
        'second_event_date_from' => null,
        'second_event_date_to' => null,
        'second_event_place' => null,
    ]);

    assertTrue($first_marriage->hasFirstEvent());
    assertFalse($first_marriage->hasSecondEvent());

    $second_marriage = factory(Marriage::class)->create([
        'first_event_type' => 'concordat_marriage',
        'first_event_date_from' => null,
        'first_event_date_to' => null,
        'first_event_place' => null,
        'second_event_type' => null,
        'second_event_date_from' => '1863-01-31',
        'second_event_date_to' => '1863-01-31',
        'second_event_place' => null,
    ]);

    assertTrue($second_marriage->hasFirstEvent());
    assertTrue($second_marriage->hasSecondEvent());

    $third_marriage = factory(Marriage::class)->create([
        'first_event_type' => null,
        'first_event_date_from' => null,
        'first_event_date_to' => null,
        'first_event_place' => 'Lwów, Litwa',
        'second_event_type' => 'civil_marriage',
        'second_event_date_from' => '1863-01-31',
        'second_event_date_to' => '1863-01-31',
        'second_event_place' => null,
    ]);

    assertTrue($third_marriage->hasFirstEvent());
    assertTrue($third_marriage->hasSecondEvent());
});
