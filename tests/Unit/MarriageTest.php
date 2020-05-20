<?php

namespace Tests\Unit;

use App\Enums\MarriageEventTypeEnum;
use App\Enums\MarriageRiteEnum;
use App\Marriage;
use App\Person;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarriageTest extends TestCase
{
    use RefreshDatabase;


    public function testCanDetermineItsVisibility()
    {
        $marriage = factory(Marriage::class)->create();

        $this->assertFalse($marriage->isVisible());
        $marriage->woman->changeVisibility(true);
        $this->assertFalse($marriage->isVisible());
        $marriage->man->changeVisibility(true);
        $this->assertTrue($marriage->isVisible());
    }

    public function testTellsIfCanBeViewedByGivenUser()
    {
        $user = factory(User::class)->create();

        $hiddenMarriage = factory(Marriage::class)->create();

        $visibleMarriage = factory(Marriage::class)->create();
        $visibleMarriage->woman->changeVisibility(true);
        $visibleMarriage->man->changeVisibility(true);

        $this->assertFalse($hiddenMarriage->canBeViewedBy($user));
        $this->assertTrue($visibleMarriage->canBeViewedBy($user));

        $user->permissions = 1;

        $this->assertTrue($hiddenMarriage->canBeViewedBy($user));
        $this->assertTrue($visibleMarriage->canBeViewedBy($user));
    }

    public function testTellsIfCanBeViewedByGuest()
    {
        $marriage = factory(Marriage::class)->create();

        $this->assertFalse($marriage->canBeViewedBy(null));

        $marriage->woman->changeVisibility(true);
        $marriage->man->changeVisibility(true);

        $this->assertTrue($marriage->canBeViewedBy(null));
    }

    public function testItCastsRiteToEnum()
    {
        $marriage = factory(Marriage::class)->create([
            'rite' => 'roman_catholic',
        ]);

        $this->assertInstanceOf(MarriageRiteEnum::class, $marriage->rite);
        $this->assertTrue($marriage->rite->isEqual(MarriageRiteEnum::roman_catholic()));
    }

    public function testRiteIsNullable()
    {
        $marriage = factory(Marriage::class)->create([
            'rite' => null,
        ]);

        $this->assertNull($marriage->rite);
    }

    public function testItCastsEventsTypesToEnums()
    {
        $marriage = factory(Marriage::class)->create([
            'first_event_type' => 'civil_marriage',
            'second_event_type' => 'church_marriage',
        ]);

        $this->assertInstanceOf(MarriageEventTypeEnum::class, $marriage->first_event_type);
        $this->assertTrue($marriage->first_event_type->isEqual(MarriageEventTypeEnum::civil_marriage()));

        $this->assertInstanceOf(MarriageEventTypeEnum::class, $marriage->second_event_type);
        $this->assertTrue($marriage->second_event_type->isEqual(MarriageEventTypeEnum::church_marriage()));
    }

    public function testEventsTypesAreNullable()
    {
        $marriage = factory(Marriage::class)->create([
            'first_event_type' => null,
            'second_event_type' => null,
        ]);

        $this->assertNull($marriage->first_event_type);
        $this->assertNull($marriage->second_event_type);
    }

    public function testCanGetManAndWoman()
    {
        $woman = factory(Person::class)->states('woman')->create();
        $man = factory(Person::class)->states('man')->create();
        $marriage = factory(Marriage::class)->create([
            'woman_id' => $woman->id,
            'man_id' => $man->id,
        ]);

        $this->assertTrue($woman->is($marriage->woman));
        $this->assertTrue($man->is($marriage->man));
    }

    public function testCanGetPartner()
    {
        $woman = factory(Person::class)->states('woman')->create();
        $man = factory(Person::class)->states('man')->create();
        $marriage = factory(Marriage::class)->create([
            'woman_id' => $woman->id,
            'man_id' => $man->id,
        ]);

        $this->assertTrue($woman->is($marriage->partner($man)));
        $this->assertTrue($man->is($marriage->partner($woman)));
    }

    public function testCanGetOrderInGivenPersonMarriages()
    {
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

        $this->assertNull($first_marriage->order($woman));
        $this->assertEquals(1, $first_marriage->order($man));
        $this->assertEquals(2, $second_marriage->order($man));
    }

    public function testItCanDetermineIfHasEvents()
    {
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

        $this->assertTrue($first_marriage->hasFirstEvent());
        $this->assertFalse($first_marriage->hasSecondEvent());

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

        $this->assertTrue($second_marriage->hasFirstEvent());
        $this->assertTrue($second_marriage->hasSecondEvent());

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

        $this->assertTrue($third_marriage->hasFirstEvent());
        $this->assertTrue($third_marriage->hasSecondEvent());
    }
}
