<?php

use App\Models\Person;

it('can list first letters', function () {
    Person::factory(4)->sequence(
        ['family_name' => 'Šott', 'last_name' => null],
        ['family_name' => 'Żygowska', 'last_name' => 'Šott'],
        ['family_name' => 'Mazowiecki', 'last_name' => null],
        ['family_name' => 'Major', 'last_name' => 'Hoffman'],
    )->create();

    expect(
        Person::letters('family')
            ->map(fn (stdClass $std) => (array) $std)
            ->all(),
    )->toBe([
        ['letter' => 'M', 'total' => 2],
        ['letter' => 'Š', 'total' => 1],
        ['letter' => 'Ż', 'total' => 1],
    ]);

    expect(
        Person::letters('last')
            ->map(fn (stdClass $std) => (array) $std)
            ->all(),
    )->toBe([
        ['letter' => 'H', 'total' => 1],
        ['letter' => 'M', 'total' => 1],
        ['letter' => 'Š', 'total' => 2],
    ]);
});
