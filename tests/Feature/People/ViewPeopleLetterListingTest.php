<?php

namespace Tests\Feature\People;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class ViewPeopleLetterListingTest extends TestCase
{
    #[TestDox('it works with no people')]
    public function testEmpty(): void
    {
        $this->get('/people/f/H')->assertNotFound();
    }

    #[TestDox('it works with people')]
    public function testOk(): void
    {
        $this->withPermissions(1);

        $people = Person::factory()->createMany([
            [
                'family_name' => 'Zbyrowski',
                'last_name' => null,
            ],
            [
                'family_name' => 'Ziobro',
                'last_name' => 'Mikke',
            ],
        ]);

        $expectedLetters = [
            'family' => [
                ['letter' => 'Z', 'count' => 2],
            ],
            'last' => [
                ['letter' => 'M', 'count' => 1],
                ['letter' => 'Z', 'count' => 1],
            ],
        ];

        $expectedPeople = [
            [
                'id' => 1,
                'visible' => true,
                'name' => $people[0]->name,
                'familyName' => 'Zbyrowski',
                'lastName' => null,
                'isDead' => $people[0]->dead,
                'birthYear' => $people[0]->birth_year,
                'deathYear' => $people[0]->death_year,
                'pytlewskiUrl' => null,
                'wielcyUrl' => null,
                'perm' => ['update' => false],
            ],
            [
                'id' => 2,
                'visible' => true,
                'name' => $people[1]->name,
                'familyName' => 'Ziobro',
                'lastName' => 'Mikke',
                'isDead' => $people[1]->dead,
                'birthYear' => $people[1]->birth_year,
                'deathYear' => $people[1]->death_year,
                'pytlewskiUrl' => null,
                'wielcyUrl' => null,
                'perm' => ['update' => false],
            ],
        ];

        $this->get('/people/f/Z')->assertInertiaOk([
            'people' => $expectedPeople,
            'letters' => $expectedLetters,
            'activeType' => 'f',
            'activeLetter' => 'Z',
        ], 'People/Letter');

        $this->get('/people/l/Z')->assertInertiaOk([
            'people' => [$expectedPeople[0]],
            'letters' => $expectedLetters,
            'activeType' => 'l',
            'activeLetter' => 'Z',
        ], 'People/Letter');

        $this->get('/people/l/M')->assertInertiaOk([
            'people' => [$expectedPeople[1]],
            'letters' => $expectedLetters,
            'activeType' => 'l',
            'activeLetter' => 'M',
        ], 'People/Letter');
    }

    #[TestDox('it hides sensitive data to guests')]
    public function testGuest(): void
    {
        Person::factory()->alive()->create([
            'family_name' => 'Ziobro',
            'last_name' => 'Mikke',
        ]);

        $this->get('/people/f/Z')->assertInertiaOk([
            'people' => [
                [
                    'id' => 1,
                    'visible' => false,
                    'pytlewskiUrl' => null,
                    'wielcyUrl' => null,
                    'perm' => ['update' => false],
                ],
            ],
            'letters' => [
                'family' => [
                    ['letter' => 'Z', 'count' => 1],
                ],
                'last' => [
                    ['letter' => 'M', 'count' => 1],
                ],
            ],
            'activeType' => 'f',
            'activeLetter' => 'Z',
        ], 'People/Letter');
    }
}
