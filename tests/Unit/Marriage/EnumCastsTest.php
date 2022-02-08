<?php

namespace Tests\Unit\Marriage;

use App\Enums\MarriageEventType;
use App\Enums\MarriageRite;
use App\Models\Marriage;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class EnumCastsTest extends TestCase
{
    #[TestDox('it casts rite to enum')]
    public function testRiteCast(): void
    {
        $marriage = Marriage::factory()->create(['rite' => 'roman_catholic']);

        $this->assertSame(MarriageRite::RomanCatholic, $marriage->rite);
    }

    #[TestDox('rite is nullable')]
    public function testRiteNullable(): void
    {
        $marriage = Marriage::factory()->create(['rite' => null]);

        $this->assertNull($marriage->rite);
    }

    #[TestDox('it casts event types to enums')]
    public function testEventTypes(): void
    {
        $marriage = Marriage::factory()->create([
            'first_event_type' => 'civil_marriage',
            'second_event_type' => 'church_marriage',
        ]);

        $this->assertSame(MarriageEventType::CivilMarriage, $marriage->first_event_type);
        $this->assertSame(MarriageEventType::ChurchMarriage, $marriage->second_event_type);
    }

    #[TestDox('events types are nullable')]
    public function testEventsNullable(): void
    {
        $marriage = Marriage::factory()->create([
            'first_event_type' => null,
            'second_event_type' => null,
        ]);

        $this->assertNull($marriage->first_event_type);
        $this->assertNull($marriage->second_event_type);
    }
}
