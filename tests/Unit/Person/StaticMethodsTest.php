<?php

namespace Tests\Unit\Person;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class StaticMethodsTest extends TestCase
{
    #[TestDox('it can list first letters')]
    public function testLetters(): void
    {
        Person::factory(4)->sequence(
            ['family_name' => 'Šott', 'last_name' => null],
            ['family_name' => 'Żygowska', 'last_name' => 'Šott'],
            ['family_name' => 'Mazowiecki', 'last_name' => null],
            ['family_name' => 'Major', 'last_name' => 'Hoffman'],
        )->create();

        $this->assertSame(
            [
                ['letter' => 'M', 'count' => 2],
                ['letter' => 'Š', 'count' => 1],
                ['letter' => 'Ż', 'count' => 1],
            ],
            Person::letters('family')->map(fn ($l) => (array) $l)->all(),
        );

        $this->assertSame(
            [
                ['letter' => 'H', 'count' => 1],
                ['letter' => 'M', 'count' => 1],
                ['letter' => 'Š', 'count' => 2],
            ],
            Person::letters('last')->map(fn ($l) => (array) $l)->all(),
        );
    }
}
