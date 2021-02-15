<?php

use App\Models\Person;

it('adds year getters', function () {
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

    expect($person->birth_year)->toBe(1957);
    expect($person->death_year)->toBe(2020);
    expect($person->funeral_year)->toBeNull();
    expect($person->burial_year)->toBeNull();
});

it('adds date getters', function () {
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

    expect($person->birth_date)->toBe('1957-05-20');
    expect($person->death_date)->toBe('between 2020-01-01 and 2020-01-07');
    expect($person->funeral_date)->toBeNull();
    expect($person->burial_date)->toBe('2020-2021');
});
