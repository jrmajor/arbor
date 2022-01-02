<?php

namespace Tests\Unit\Person;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class AgeHelpersTest extends TestCase
{
    #[TestDox('it returns null when calculating age without date')]
    public function testAgeWithoutDate(): void
    {
        $person = Person::factory()->createOne([
            'birth_date_from' => null,
            'birth_date_to' => null,
        ]);

        $at = carbon(2019, 8, 15);

        $this->assertNull($person->age($at, true));
        $this->assertNull($person->age($at));
    }

    #[TestDox('it can calculate age with complete dates')]
    public function testAgeWithDates(): void
    {
        $person = Person::factory()->createOne([
            'birth_date_from' => '1994-06-22',
            'birth_date_to' => '1994-06-22',
        ]);

        $at = carbon(2019, 8, 15);

        $this->assertSame(25, $person->age($at, true));
        $this->assertSame(25, $person->age($at));
    }

    #[TestDox('it can calculate age with incomplete birth date')]
    public function testAgeIncompleteDate(): void
    {
        $withoutDay = Person::factory()->createOne([
            'birth_date_from' => '1978-04-01',
            'birth_date_to' => '1978-04-30',
        ]);

        $withoutMonth = Person::factory()->createOne([
            'birth_date_from' => '1982-01-01',
            'birth_date_to' => '1982-12-31',
        ]);

        $differentMonth = carbon(2017, 6, 15);

        $sameMonth = carbon(2006, 4, 16);

        $this->assertSame(39, $withoutDay->age($differentMonth, true));
        $this->assertSame(39, $withoutDay->age($differentMonth));
        $this->assertSame(28, $withoutDay->age($sameMonth, true)); // 27-28
        $this->assertSame('27-28', $withoutDay->age($sameMonth));

        $this->assertSame(35, $withoutMonth->age($differentMonth, true)); // 34-35
        $this->assertSame('34-35', $withoutMonth->age($differentMonth));
    }

    #[TestDox('it can calculate age with incomplete at date')]
    public function testAgeIncompleteAtDate(): void
    {
        $person = Person::factory()->createOne([
            'birth_date_from' => '1975-03-22',
            'birth_date_to' => '1975-03-22',
        ]);

        $withoutDay = [carbon(2013, 7, 1), carbon(2013, 7, 31)];

        $withoutDaySameMonth = [carbon(2015, 3, 1), carbon(2015, 3, 31)];

        $withoutMonth = [carbon(2016, 1, 1), carbon(2016, 12, 31)];

        $this->assertSame(38, $person->age($withoutDay, true));
        $this->assertSame(38, $person->age($withoutDay));
        $this->assertSame(40, $person->age($withoutDaySameMonth, true)); // 39-40
        $this->assertSame('39-40', $person->age($withoutDaySameMonth));
        $this->assertSame(41, $person->age($withoutMonth, true)); // 40-41
        $this->assertSame('40-41', $person->age($withoutMonth));
    }

    #[TestDox('it can calculate age with incomplete dates')]
    public function testAgeIncompleteDates(): void
    {
        $person = Person::factory()->createOne([
            'birth_date_from' => '1992-01-01',
            'birth_date_to' => '1992-12-31',
        ]);

        $at = [carbon(2010, 7, 1), carbon(2010, 7, 31)];

        $this->assertSame(18, $person->age($at, true)); // 17-18
        $this->assertSame('17-18', $person->age($at));
    }

    #[TestDox('it can calculate current age')]
    public function testCurrentAge(): void
    {
        $person = Person::factory()->createOne([
            'birth_date_from' => '1973-05-12',
            'birth_date_to' => '1973-05-12',
        ]);

        $this->travelTo(carbon('2016-11-10'));

        $this->assertSame('2016-11-10', now()->format('Y-m-d'));
        $this->assertSame(43, $person->currentAge(true));
        $this->assertSame(43, $person->currentAge());

        $this->travelBack();
    }

    #[TestDox('it can calculate age age at death')]
    public function testAgeAtDeath(): void
    {
        $person = Person::factory()->createOne([
            'birth_date_from' => '1874-04-08',
            'birth_date_to' => '1874-04-08',
            'death_date_from' => '1941-05-30',
            'death_date_to' => '1941-05-30',
        ]);

        $this->assertSame(67, $person->ageAtDeath(true));
        $this->assertSame(67, $person->ageAtDeath());
    }
}
