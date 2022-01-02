<?php

namespace Tests\Unit\Person;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class NameFormattersTest extends TestCase
{
    #[TestDox('it can format simple name')]
    public function testFormatName(): void
    {
        $person = Person::factory()->alive()->createOne([
            'name' => 'Zenona',
            'middle_name' => 'Ludmiła',
            'family_name' => 'Skwierczyńska',
            'last_name' => null,
        ]);

        $this->assertSame('Zenona Skwierczyńska', $person->formatSimpleName());

        $person->last_name = 'Wojtyła';

        $this->assertSame('Zenona Wojtyła (Skwierczyńska)', $person->formatSimpleName());
    }

    #[TestDox('it can format simple dates')]
    public function testFormatDates(): void
    {
        $person = Person::factory()->alive()->createOne([
            'birth_date_from' => null,
            'birth_date_to' => null,
        ]);

        $this->assertNull($person->formatSimpleDates());

        $person->birth_date_from = '1913-05-01';
        $person->birth_date_to = '1913-05-01';
        $this->assertSame('∗1913', $person->formatSimpleDates());

        $person->dead = true;
        $person->death_date_from = '1945-01-01';
        $person->death_date_to = '1945-12-31';
        $this->assertSame('∗1913, ✝1945', $person->formatSimpleDates());

        $person->birth_date_from = null;
        $person->birth_date_to = null;
        $this->assertSame('✝1945', $person->formatSimpleDates());
    }
}
