<?php

namespace Tests\Feature\People;

use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class PeopleSearchTest extends TestCase
{
    /** @var Collection<int, Person> */
    private Collection $people;

    protected function setUp(): void
    {
        parent::setUp();

        $this->people = Person::factory(3)
            ->alive()
            ->sequence([
                'name' => 'Jan',
                'family_name' => 'Major',
                'last_name' => null,
                'visibility' => true,
                'birth_date_from' => null,
                'birth_date_to' => null,
            ], [
                'name' => 'Balbina',
                'family_name' => 'Bosak',
                'last_name' => 'Gąsiorowska',
                'visibility' => false,
                'birth_date_from' => null,
                'birth_date_to' => null,
            ], [
                'name' => 'Nepomucena',
                'family_name' => 'Korwin',
                'last_name' => 'Major',
                'visibility' => false,
                'birth_date_from' => null,
                'birth_date_to' => null,
            ])
            ->create();
    }

    #[TestDox('it works with no query')]
    public function testNoQuery(): void
    {
        $this->get('people/search')
            ->assertStatus(200)
            ->assertExactJson([]);
    }

    #[TestDox('it hides sensitive data from guests')]
    public function testGuest(): void
    {
        $firstPerson = $this->people[0];

        $this->get('people/search?search=maj')
            ->assertStatus(200)
            ->assertExactJson([
                [
                    'id' => $firstPerson->id,
                    'name' => $firstPerson->formatSimpleName(),
                    'dates' => $firstPerson->formatSimpleDates(),
                    'url' => route('people.show', $firstPerson->id),
                ],
            ]);
    }

    #[TestDox('it shows full search results to users with permissions')]
    public function testOk(): void
    {
        $firstPerson = $this->people[0];
        $secondPerson = $this->people[2];

        $this->withPermissions(1)
            ->get('people/search?search=maj')
            ->assertStatus(200)
            ->assertExactJson([
                [
                    'id' => $firstPerson->id,
                    'name' => $firstPerson->formatSimpleName(),
                    'dates' => $firstPerson->formatSimpleDates(),
                    'url' => route('people.show', $firstPerson->id),
                ],
                [
                    'id' => $secondPerson->id,
                    'name' => $secondPerson->formatSimpleName(),
                    'dates' => $secondPerson->formatSimpleDates(),
                    'url' => route('people.show', $secondPerson->id),
                ],
            ]);
    }
}
