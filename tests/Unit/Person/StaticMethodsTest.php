<?php

namespace Tests\Unit\Person;

use App\Models\Letter;
use App\Models\Person;
use Illuminate\Support\Facades\Cache;
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

    #[TestDox('cached letters survive a round trip through a serializing store')]
    public function testLettersCacheRoundTrip(): void
    {
        config([
            'cache.stores.serializing' => ['driver' => 'array', 'serialize' => true],
            'cache.default' => 'serializing',
        ]);

        Person::factory(3)->sequence(
            ['family_name' => 'Šott'],
            ['family_name' => 'Mazowiecki'],
            ['family_name' => 'Major'],
        )->create();

        $expected = [
            ['letter' => 'M', 'count' => 2],
            ['letter' => 'Š', 'count' => 1],
        ];

        $this->assertSame($expected, Person::letters('family')->map(fn ($l) => (array) $l)->all());
        $this->assertTrue(Cache::has('letters.family'));
        $this->assertContainsOnlyInstancesOf(Letter::class, Person::letters('family'));
        $this->assertSame($expected, Person::letters('family')->map(fn ($l) => (array) $l)->all());
    }
}
