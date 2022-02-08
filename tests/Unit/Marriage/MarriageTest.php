<?php

namespace Tests\Unit\Marriage;

use App\Models\Marriage;
use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class MarriageTest extends TestCase
{
    #[TestDox('it can get man and woman')]
    public function testManWoman(): void
    {
        $marriage = Marriage::factory()->create([
            'woman_id' => $woman = Person::factory()->female()->create(),
            'man_id' => $man = Person::factory()->male()->create(),
        ]);

        $this->assertSameModel($woman, $marriage->woman());
        $this->assertSameModel($man, $marriage->man());
    }

    #[TestDox('it can get partner')]
    public function testPartner(): void
    {
        $marriage = Marriage::factory()->create([
            'woman_id' => $woman = Person::factory()->female()->create(),
            'man_id' => $man = Person::factory()->male()->create(),
        ]);

        $this->assertSameModel($woman, $marriage->partner($man));
        $this->assertSameModel($man, $marriage->partner($woman));
    }

    #[TestDox('it can get order in given person marriage')]
    public function testOrder(): void
    {
        $woman = Person::factory()->female()->create();
        $man = Person::factory()->male()->create();

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

        $this->assertNull($firstMarriage->order($woman));
        $this->assertSame(1, $firstMarriage->order($man));
        $this->assertSame(2, $secondMarriage->order($man));
    }

    #[TestDox('it can determine if has events')]
    public function testEvents(): void
    {
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

        $this->assertTrue($firstMarriage->hasFirstEvent());
        $this->assertFalse($firstMarriage->hasSecondEvent());

        $this->assertTrue($secondMarriage->hasFirstEvent());
        $this->assertTrue($secondMarriage->hasSecondEvent());

        $this->assertTrue($thirdMarriage->hasFirstEvent());
        $this->assertTrue($thirdMarriage->hasSecondEvent());
    }
}
