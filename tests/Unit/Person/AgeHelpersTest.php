<?php

use App\Models\Person;
use Carbon\Carbon;
use function Pest\Laravel\travelBack;
use function Pest\Laravel\travelTo;

test('year getters work', function () {
    $personWithDates = Person::factory()->dead()->create([
        'birth_date_from' => '1957-05-20',
        'birth_date_to' => '1957-05-20',
        'death_date_from' => '2020-11-09',
        'death_date_to' => '2020-11-09',
    ]);
    $personWithSomeDates = Person::factory()->dead()->create([
        'birth_date_from' => '1893-01-01',
        'birth_date_to' => '1893-12-31',
        'death_date_from' => '1944-08-01',
        'death_date_to' => '1944-08-31',
    ]);
    $personWithoutDates = Person::factory()->create([
        'birth_date_from' => null,
        'birth_date_to' => null,
        'death_date_from' => null,
        'death_date_to' => null,
    ]);

    expect($personWithDates->birth_year)->toBe(1957);
    expect($personWithDates->death_year)->toBe(2020);

    expect($personWithSomeDates->birth_year)->toBe(1893);
    expect($personWithSomeDates->death_year)->toBe(1944);

    expect($personWithoutDates->birth_year)->toBeNull();
    expect($personWithoutDates->death_year)->toBeNull();
});

it('returns null when calculating age without date', function () {
    $person = Person::factory()->create([
        'birth_date_from' => null,
        'birth_date_to' => null,
    ]);

    $at = Carbon::create(2019, 8, 15);

    expect($person->age($at, true))->toBeNull();
    expect($person->age($at))->toBeNull();
});

it('can calculate age with complete dates', function () {
    $person = Person::factory()->create([
        'birth_date_from' => '1994-06-22',
        'birth_date_to' => '1994-06-22',
    ]);

    $at = Carbon::create(2019, 8, 15);

    expect($person->age($at, true))->toBe(25);
    expect($person->age($at))->toBe(25);
});

it('can calculate age with incomplete birth date', function () {
    $person_without_day = Person::factory()->create([
        'birth_date_from' => '1978-04-01',
        'birth_date_to' => '1978-04-30',
    ]);

    $person_without_month = Person::factory()->create([
        'birth_date_from' => '1982-01-01',
        'birth_date_to' => '1982-12-31',
    ]);

    $at_diffrent_month = Carbon::create(2017, 6, 15);

    $at_same_month = Carbon::create(2006, 4, 16);

    expect($person_without_day->age($at_diffrent_month, true))->toBe(39);
    expect($person_without_day->age($at_diffrent_month))->toBe(39);
    expect($person_without_day->age($at_same_month, true))->toBe(28); // 27-28
    expect($person_without_day->age($at_same_month))->toBe('27-28');
    expect($person_without_month->age($at_diffrent_month, true))->toBe(35); // 34-35
    expect($person_without_month->age($at_diffrent_month))->toBe('34-35');
});

it('can calculate age with incomplete at date', function () {
    $person = Person::factory()->create([
        'birth_date_from' => '1975-03-22',
        'birth_date_to' => '1975-03-22',
    ]);

    $without_day = [Carbon::create(2013, 7, 01), Carbon::create(2013, 7, 31)];

    $without_day_in_same_month = [Carbon::create(2015, 3, 01), Carbon::create(2015, 3, 31)];

    $without_month = [Carbon::create(2016, 01, 01), Carbon::create(2016, 12, 31)];

    expect($person->age($without_day, true))->toBe(38);
    expect($person->age($without_day))->toBe(38);
    expect($person->age($without_day_in_same_month, true))->toBe(40); // 39-40
    expect($person->age($without_day_in_same_month))->toBe('39-40');
    expect($person->age($without_month, true))->toBe(41); // 40-41
    expect($person->age($without_month))->toBe('40-41');
});

it('can calculate age with incomplete dates', function () {
    $person = Person::factory()->create([
        'birth_date_from' => '1992-01-01',
        'birth_date_to' => '1992-12-31',
    ]);

    $at = [Carbon::create(2010, 7, 01), Carbon::create(2010, 7, 31)];

    expect($person->age($at, true))->toBe(18); // 17-18
    expect($person->age($at))->toBe('17-18');
});

it('can calculate current age', function () {
    $person = Person::factory()->create([
        'birth_date_from' => '1973-05-12',
        'birth_date_to' => '1973-05-12',
    ]);

    travelTo(Carbon::create('2016-11-10'));

    expect(Carbon::now()->format('Y-m-d'))->toBe('2016-11-10');
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
