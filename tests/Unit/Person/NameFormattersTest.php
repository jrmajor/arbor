<?php

use App\Models\Person;

it('can format name', function () {
    $person = Person::factory()->alive()->create([
        'name' => 'Zenona',
        'middle_name' => 'Ludmiła',
        'family_name' => 'Skwierczyńska',
        'last_name' => null,
        'birth_date_from' => null,
        'birth_date_to' => null,
    ]);

    expect($person->formatName())->toBe("Zenona Skwierczyńska [№$person->id]");

    $person->last_name = 'Wojtyła';
    expect($person->formatName())->toBe("Zenona Wojtyła (Skwierczyńska) [№$person->id]");
    $person->last_name = null;

    $person->birth_date_from = '1913-05-01';
    $person->birth_date_to = '1913-05-01';
    expect($person->formatName())->toBe("Zenona Skwierczyńska (∗1913) [№$person->id]");

    $person->dead = true;
    $person->death_date_from = '1945-01-01';
    $person->death_date_to = '1945-12-31';
    expect($person->formatName())->toBe("Zenona Skwierczyńska (∗1913, ✝1945) [№$person->id]");

    $person->birth_date_from = null;
    $person->birth_date_to = null;
    expect($person->formatName())->toBe("Zenona Skwierczyńska (✝1945) [№$person->id]");
});

it('can format simple name', function () {
    $person = Person::factory()->alive()->create([
        'name' => 'Zenona',
        'middle_name' => 'Ludmiła',
        'family_name' => 'Skwierczyńska',
        'last_name' => null,
    ]);

    expect($person->formatSimpleName())->toBe('Zenona Skwierczyńska');

    $person->last_name = 'Wojtyła';

    expect($person->formatSimpleName())->toBe('Zenona Wojtyła (Skwierczyńska)');
});
