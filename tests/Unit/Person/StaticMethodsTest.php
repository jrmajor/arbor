<?php

use App\Models\Person;

it('can be found by pytlewski id', function () {
    $person = Person::factory()->create([
        'id_pytlewski' => 1140,
    ]);

    Person::factory()->create([
        'id_pytlewski' => null,
    ]);

    expect(Person::findByPytlewskiId(1140))->toBeModel($person);
    expect(Person::findByPytlewskiId(null))->toBeNull();
    expect(Person::findByPytlewskiId(2137))->toBeNull();
});

it('can list first letters', function () {
    collect([
        [
            'family_name' => 'Šott',
            'last_name' => null,
        ],
        [
            'family_name' => 'Żygowska',
            'last_name' => 'Šott',
        ],
        [
            'family_name' => 'Mazowiecki',
            'last_name' => null,
        ],
        [
            'family_name' => 'Major',
            'last_name' => 'Hoffman',
        ],
    ])->each(function ($names) {
        Person::factory()->create($names);
    });

    expect(Person::letters('family')->map(fn ($std) => (array) $std)->toArray())
        ->toBe([
            ['letter' => 'M', 'total' => 2],
            ['letter' => 'Š', 'total' => 1],
            ['letter' => 'Ż', 'total' => 1],
        ]);

    expect(Person::letters('last')->map(fn ($std) => (array) $std)->toArray())
        ->toBe([
            ['letter' => 'H', 'total' => 1],
            ['letter' => 'M', 'total' => 1],
            ['letter' => 'Š', 'total' => 2],
        ]);
});
