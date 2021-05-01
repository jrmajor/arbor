<?php

use App\Models\Person;
use function Pest\Laravel\travelBack;
use function Pest\Laravel\travelTo;

it('returns null when calculating age without date', function () {
    $person = Person::factory()->create([
        'birth_date_from' => null,
        'birth_date_to' => null,
    ]);

    $at = carbon(2019, 8, 15);

    expect($person->age($at, true))->toBeNull();
    expect($person->age($at))->toBeNull();
});

it('can calculate age with complete dates', function () {
    $person = Person::factory()->create([
        'birth_date_from' => '1994-06-22',
        'birth_date_to' => '1994-06-22',
    ]);

    $at = carbon(2019, 8, 15);

    expect($person->age($at, true))->toBe(25);
    expect($person->age($at))->toBe(25);
});

it('can calculate age with incomplete birth date', function () {
    $withoutDay = Person::factory()->create([
        'birth_date_from' => '1978-04-01',
        'birth_date_to' => '1978-04-30',
    ]);

    $withoutMonth = Person::factory()->create([
        'birth_date_from' => '1982-01-01',
        'birth_date_to' => '1982-12-31',
    ]);

    $differentMonth = carbon(2017, 6, 15);

    $sameMonth = carbon(2006, 4, 16);

    expect($withoutDay->age($differentMonth, true))->toBe(39);
    expect($withoutDay->age($differentMonth))->toBe(39);
    expect($withoutDay->age($sameMonth, true))->toBe(28); // 27-28
    expect($withoutDay->age($sameMonth))->toBe('27-28');
    expect($withoutMonth->age($differentMonth, true))->toBe(35); // 34-35
    expect($withoutMonth->age($differentMonth))->toBe('34-35');
});

it('can calculate age with incomplete at date', function () {
    $person = Person::factory()->create([
        'birth_date_from' => '1975-03-22',
        'birth_date_to' => '1975-03-22',
    ]);

    $withoutDay = [carbon(2013, 7, 01), carbon(2013, 7, 31)];

    $withoutDaySameMonth = [carbon(2015, 3, 01), carbon(2015, 3, 31)];

    $withoutMonth = [carbon(2016, 01, 01), carbon(2016, 12, 31)];

    expect($person->age($withoutDay, true))->toBe(38);
    expect($person->age($withoutDay))->toBe(38);
    expect($person->age($withoutDaySameMonth, true))->toBe(40); // 39-40
    expect($person->age($withoutDaySameMonth))->toBe('39-40');
    expect($person->age($withoutMonth, true))->toBe(41); // 40-41
    expect($person->age($withoutMonth))->toBe('40-41');
});

it('can calculate age with incomplete dates', function () {
    $person = Person::factory()->create([
        'birth_date_from' => '1992-01-01',
        'birth_date_to' => '1992-12-31',
    ]);

    $at = [carbon(2010, 7, 01), carbon(2010, 7, 31)];

    expect($person->age($at, true))->toBe(18); // 17-18
    expect($person->age($at))->toBe('17-18');
});

it('can calculate current age', function () {
    $person = Person::factory()->create([
        'birth_date_from' => '1973-05-12',
        'birth_date_to' => '1973-05-12',
    ]);

    travelTo(carbon('2016-11-10'));

    expect(now()->format('Y-m-d'))->toBe('2016-11-10');
    expect($person->currentAge(true))->toBe(43);
    expect($person->currentAge())->toBe(43);

    travelBack();
});

it('can calculate age age at death', function () {
    $person = Person::factory()->create([
        'birth_date_from' => '1874-04-08',
        'birth_date_to' => '1874-04-08',
        'death_date_from' => '1941-05-30',
        'death_date_to' => '1941-05-30',
    ]);

    expect($person->ageAtDeath(true))->toBe(67);
    expect($person->ageAtDeath())->toBe(67);
});
