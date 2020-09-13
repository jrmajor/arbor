<?php

namespace Tests\Unit;

use App\Models\Marriage;
use App\Models\Person;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use function Pest\Laravel\travelTo;
use function Pest\Laravel\travelBack;

it('can determine its visibility', function () {
    $alive = Person::factory()->alive()->create();
    $dead = Person::factory()->dead()->create();

    $alive->changeVisibility(false);
    expect($alive->isVisible())->toBeFalse();
    $alive->changeVisibility(true);
    expect($alive->isVisible())->toBeTrue();

    $dead->changeVisibility(false);
    expect($dead->isVisible())->toBeFalse();
    $dead->changeVisibility(true);
    expect($dead->isVisible())->toBeTrue();
});

test('change visibility method works', function () {
    $person = Person::factory()->create();

    expect($person->isVisible())->toBeFalse();

    $person->changeVisibility(true);

    expect($person->isVisible())->toBeTrue();

    $person->changeVisibility(false);

    expect($person->isVisible())->toBeFalse();
});

it('tells if it can be viewed by given user', function () {
    $user = User::factory()->create();

    $hiddenPerson = Person::factory()->create([
        'visibility' => false,
    ]);

    $visiblePerson = Person::factory()->create([
        'visibility' => true,
    ]);

    expect($hiddenPerson->canBeViewedBy($user))->toBeFalse();
    expect($visiblePerson->canBeViewedBy($user))->toBeTrue();

    $user->permissions = 1;

    expect($hiddenPerson->canBeViewedBy($user))->toBeTrue();
    expect($visiblePerson->canBeViewedBy($user))->toBeTrue();
});

it('tells if can be viewed by guest', function () {
    $person = Person::factory()->create();

    expect($person->canBeViewedBy(null))->toBeFalse();

    $person->changeVisibility(true);

    expect($person->canBeViewedBy(null))->toBeTrue();
});

it('can get mother', function () {
    $mother = Person::factory()->woman()->create();
    $person = Person::factory()->create(['mother_id' => $mother->id]);

    expect($person->mother->id)->toBe($mother->id);
});

it('can get father', function () {
    $father = Person::factory()->man()->create();
    $person = Person::factory()->create(['father_id' => $father->id]);

    expect($person->father->id)->toBe($father->id);
});

it('can get siblings and half siblings', function () {
    $mother = Person::factory()->woman()->create();
    $father = Person::factory()->man()->create();

    $person = Person::factory()->create([
        'mother_id' => $mother->id,
        'father_id' => $father->id,
    ]);

    Person::factory()->count(2)->create([
        'mother_id' => $person->mother_id,
        'father_id' => $person->father_id,
    ]);

    Person::factory()->count(1)->create([
        'mother_id' => $person->mother_id,
        'father_id' => Person::factory()->man()->create(),
    ]);
    Person::factory()->count(2)->create([
        'mother_id' => $person->mother_id,
        'father_id' => null,
    ]);

    Person::factory()->count(3)->create([
        'mother_id' => Person::factory()->woman()->create(),
        'father_id' => $person->father_id,
    ]);
    Person::factory()->count(1)->create([
        'mother_id' => null,
        'father_id' => $person->father_id,
    ]);

    expect($person->siblings)->toHaveCount(2);

    expect($person->siblings_mother)->toHaveCount(3);

    expect($person->siblings_father)->toHaveCount(4);

    $person->mother_id = null;
    $person = tap($person)->save()->fresh();

    expect($person->siblings)->toHaveCount(0);

    expect($person->siblings_mother)->toHaveCount(0);

    expect($person->siblings_father)->toHaveCount(6);
});

it('can get marriages', function () {
    $person = Person::factory()->woman()->create();

    Person::factory()->count(3)->man()->create()
        ->each(function ($partner) use ($person) {
            Marriage::factory()->create([
                'woman_id' => $person->id,
                'man_id' => $partner->id,
            ]);
        });

    expect($person->marriages)->toHaveCount(3);
});

it('can get partners', function () {
    $person = Person::factory()->woman()->create();

    $spouse = Person::factory()->man()->create(['name' => 'Spouse']);

    Marriage::factory()->create([
        'woman_id' => $person->id,
        'man_id' => $spouse->id,
    ]);

    $spouseWithChild = Person::factory()->man()->create(['name' => 'Spouse With Child']);

    Marriage::factory()->create([
        'woman_id' => $person->id,
        'man_id' => $spouseWithChild->id,
    ]);

    Person::factory()->create([
        'mother_id' => $person->id,
        'father_id' => $spouseWithChild->id,
    ]);

    $lover = Person::factory()->man()->create(['name' => 'Lover']);

    Person::factory()->create([
        'mother_id' => $person->id,
        'father_id' => $lover->id,
    ]);

    expect($person->partners())->toHaveCount(3);

    expect($person->partners()->contains($spouse))->toBeTrue();
    expect($person->partners()->contains($spouseWithChild))->toBeTrue();
    expect($person->partners()->contains($lover))->toBeTrue();
})->skip();

it('can get children', function () {
    $father = Person::factory()->man()->create();

    Person::factory()->count(2)->woman()->create()
        ->each(function ($mother) use ($father) {
            Person::factory()->create([
                'mother_id' => $mother->id,
                'father_id' => $father->id,
            ]);
        });

    $child = Person::factory()->create([
        'mother_id' => null,
        'father_id' => $father->id,
    ]);

    expect($father->children)->toHaveCount(3);
    expect($father->children->contains($child))->toBeTrue();
});

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

it('casts sources to collection', function () {
    $sources = Person::factory()->create([
        'sources' => null,
    ])->sources;

    expect($sources)->toBeInstanceOf(Collection::class);
    expect($sources->isEmpty())->toBeTrue();

    $sources = Person::factory()->create([
        'sources' => [],
    ])->sources;

    expect($sources)->toBeInstanceOf(Collection::class);
    expect($sources->isEmpty())->toBeTrue();

    $sources = Person::factory()->create([
        'sources' => [
            '[Henryk Gąsiorowski](https://pl.wikipedia.org/wiki/Henryk_G%C4%85siorowski) w Wikipedii, wolnej encyklopedii, dostęp 2020-06-06',
            'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
        ],
    ])->sources;

    expect($sources)->toBeInstanceOf(Collection::class);
    expect($sources)->toHaveCount(2);
});

test('sources are sanitized', function () {
    $unsanitized = [
        'a' => "     [Henryk    Gąsiorowski](https://pl.wikipedia.org/wiki/Henryk_G%C4%85siorowski)  \t   w Wikipedii,\nwolnej encyklopedii, dostęp 2020-06-06\r\n",
        'b' => "    \n",
        null,
        'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
    ];

    $sanitized = [
        '[Henryk Gąsiorowski](https://pl.wikipedia.org/wiki/Henryk_G%C4%85siorowski) w Wikipedii, wolnej encyklopedii, dostęp 2020-06-06',
        'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
    ];

    expect(
        Person::factory()->create(['sources' => $unsanitized])
            ->sources->map->raw()->all()
    )->toBe($sanitized);
});

it('can be found by pytlewski id', function () {
    $person = Person::factory()->create([
        'id_pytlewski' => 1140,
    ]);

    expect($person->is(Person::findByPytlewskiId(1140)))->toBeTrue();
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
