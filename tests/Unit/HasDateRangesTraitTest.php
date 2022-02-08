<?php

namespace Tests\Unit;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class HasDateRangesTraitTest extends TestCase
{
    #[TestDox('it adds year getters')]
    public function testYearGetters(): void
    {
        $person = Person::factory()->dead()->create([
            'birth_date_from' => '1957-05-20',
            'birth_date_to' => '1957-05-20',
            'death_date_from' => '2020-01-01',
            'death_date_to' => '2020-01-31',
            'funeral_date_from' => null,
            'funeral_date_to' => null,
            'burial_date_from' => '2020-01-01',
            'burial_date_to' => '2021-12-31',
        ]);

        $this->assertSame(1957, $person->birth_year);
        $this->assertSame(2020, $person->death_year);
        $this->assertNull($person->funeral_year);
        $this->assertNull($person->burial_year);
    }

    #[TestDox('it adds date getters')]
    public function testDateGetters(): void
    {
        $person = Person::factory()->dead()->create([
            'birth_date_from' => '1957-05-20',
            'birth_date_to' => '1957-05-20',
            'death_date_from' => '2020-01-01',
            'death_date_to' => '2020-01-07',
            'funeral_date_from' => null,
            'funeral_date_to' => null,
            'burial_date_from' => '2020-01-01',
            'burial_date_to' => '2021-12-31',
        ]);

        $this->assertSame('1957-05-20', $person->birth_date);
        $this->assertSame('between 2020-01-01 and 2020-01-07', $person->death_date);
        $this->assertNull($person->funeral_date);
        $this->assertSame('2020-2021', $person->burial_date);
    }
}
