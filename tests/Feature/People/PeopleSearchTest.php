<?php

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Sequence;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->people = Person::factory()
        ->count(3)
        ->alive()
        ->state(new Sequence(
            [
                'name' => 'Jan',
                'family_name' => 'Major',
                'last_name' => null,
                'visibility' => true,
                'birth_date_from' => null,
                'birth_date_to' => null,
            ],
            [
                'name' => 'Balbina',
                'family_name' => 'Bosak',
                'last_name' => 'Gąsiorowska',
                'visibility' => false,
                'birth_date_from' => null,
                'birth_date_to' => null,
            ],
            [
                'name' => 'Nepomucena',
                'family_name' => 'Korwin',
                'last_name' => 'Major',
                'visibility' => false,
                'birth_date_from' => null,
                'birth_date_to' => null,
            ]
        ))
        ->create();
});

it('works with no query')
    ->get('people/search')
    ->assertStatus(200)
    ->assertExactJson([]);

it('hides sensitive data from guests', function () {
    $firstPerson = $this->people[0];
    $secondPerson = $this->people[2];

    get('people/search?search=maj')
        ->assertStatus(200)
        ->assertExactJson([
            [
                'id' => $firstPerson->id,
                'name' => $firstPerson->formatSimpleName(),
                'dates' => $firstPerson->formatSimpleDates(),
                'url' => route('people.show', $firstPerson->id),
                'hidden' => false
            ],
            [
                'id' => $secondPerson->id,
                'name' => null,
                'dates' => null,
                'url' => null,
                'hidden' => true
            ],
        ]);
});

it('shows full search results to users with permissions', function () {
    $firstPerson = $this->people[0];
    $secondPerson = $this->people[2];

    withPermissions(1)
        ->get('people/search?search=maj')
        ->assertStatus(200)
        ->assertExactJson([
            [
                'id' => $firstPerson->id,
                'name' => $firstPerson->formatSimpleName(),
                'dates' => $firstPerson->formatSimpleDates(),
                'url' => route('people.show', $firstPerson->id),
                'hidden' => false
            ],
            [
                'id' => $secondPerson->id,
                'name' => $secondPerson->formatSimpleName(),
                'dates' => $secondPerson->formatSimpleDates(),
                'url' => route('people.show', $secondPerson->id),
                'hidden' => false
            ],
        ]);
});
