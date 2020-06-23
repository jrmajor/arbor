<?php

namespace Tests\Unit;

use App\Marriage;
use App\Person;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

it('can determine its visibility', function () {
    $alive = factory(Person::class)->state('alive')->create();
    $dead = factory(Person::class)->state('dead')->create();

    $alive->changeVisibility(false);
    assertFalse($alive->isVisible());
    $alive->changeVisibility(true);
    assertTrue($alive->isVisible());

    $dead->changeVisibility(false);
    assertFalse($dead->isVisible());
    $dead->changeVisibility(true);
    assertTrue($dead->isVisible());
});

test('change visibility method works', function () {
    $person = factory(Person::class)->create();

    assertFalse($person->isVisible());

    $person->changeVisibility(true);

    assertTrue($person->isVisible());

    $person->changeVisibility(false);

    assertFalse($person->isVisible());
});

it('tells if it can be viewed by given user', function () {
    $user = factory(User::class)->create();

    $hiddenPerson = factory(Person::class)->create([
        'visibility' => false,
    ]);

    $visiblePerson = factory(Person::class)->create([
        'visibility' => true,
    ]);

    assertFalse($hiddenPerson->canBeViewedBy($user));
    assertTrue($visiblePerson->canBeViewedBy($user));

    $user->permissions = 1;

    assertTrue($hiddenPerson->canBeViewedBy($user));
    assertTrue($visiblePerson->canBeViewedBy($user));
});

it('tells if can be viewed by guest', function () {
    $person = factory(Person::class)->create();

    assertFalse($person->canBeViewedBy(null));

    $person->changeVisibility(true);

    assertTrue($person->canBeViewedBy(null));
});

it('can get mother', function () {
    $mother = factory(Person::class)->state('woman')->create();
    $person = factory(Person::class)->create(['mother_id' => $mother->id]);

    assertEquals($mother->id, $person->mother->id);
});

it('can get father', function () {
    $father = factory(Person::class)->state('man')->create();
    $person = factory(Person::class)->create(['father_id' => $father->id]);

    assertEquals($father->id, $person->father->id);
});

it('can get siblings and half siblings', function () {
    $mother = factory(Person::class)->state('woman')->create();
    $father = factory(Person::class)->state('man')->create();

    $person = factory(Person::class)->create([
        'mother_id' => $mother->id,
        'father_id' => $father->id,
    ]);

    factory(Person::class, 2)->create([
        'mother_id' => $person->mother_id,
        'father_id' => $person->father_id,
    ]);

    factory(Person::class, 1)->create([
        'mother_id' => $person->mother_id,
        'father_id' => factory(Person::class)->state('man')->create(),
    ]);
    factory(Person::class, 2)->create([
        'mother_id' => $person->mother_id,
        'father_id' => null,
    ]);

    factory(Person::class, 3)->create([
        'mother_id' => factory(Person::class)->state('woman')->create(),
        'father_id' => $person->father_id,
    ]);
    factory(Person::class, 1)->create([
        'mother_id' => null,
        'father_id' => $person->father_id,
    ]);

    assertCount(2, $person->siblings);

    assertCount(3, $person->siblings_mother);

    assertCount(4, $person->siblings_father);

    $person->mother_id = null;
    $person = tap($person)->save()->fresh();

    assertCount(0, $person->siblings);

    assertCount(0, $person->siblings_mother);

    assertCount(6, $person->siblings_father);
});

it('can get marriages', function () {
    $person = factory(Person::class)->state('woman')->create();

    factory(Person::class, 3)->state('man')->create()
        ->each(function ($partner) use ($person) {
            factory(Marriage::class)->create([
                'woman_id' => $person->id,
                'man_id' => $partner->id,
            ]);
        });

    assertCount(3, $person->marriages);
});

it('can get partners', function () {
    $person = factory(Person::class)->state('woman')->create();

    $spouse = factory(Person::class)->state('man')->create(['name' => 'Spouse']);

    factory(Marriage::class)->create([
        'woman_id' => $person->id,
        'man_id' => $spouse->id,
    ]);

    $spouseWithChild = factory(Person::class)->state('man')->create(['name' => 'Spouse With Child']);

    factory(Marriage::class)->create([
        'woman_id' => $person->id,
        'man_id' => $spouseWithChild->id,
    ]);

    factory(Person::class)->create([
        'mother_id' => $person->id,
        'father_id' => $spouseWithChild->id,
    ]);

    $lover = factory(Person::class)->state('man')->create(['name' => 'Lover']);

    factory(Person::class)->create([
        'mother_id' => $person->id,
        'father_id' => $lover->id,
    ]);

    assertCount(3, $person->partners());

    assertTrue($person->partners()->contains($spouse));
    assertTrue($person->partners()->contains($spouseWithChild));
    assertTrue($person->partners()->contains($lover));
})->skip();

it('can get children', function () {
    $father = factory(Person::class)->state('man')->create();

    factory(Person::class, 2)->state('woman')->create()
        ->each(function ($mother) use ($father) {
            factory(Person::class)->create([
                'mother_id' => $mother->id,
                'father_id' => $father->id,
            ]);
        });

    $child = factory(Person::class)->create([
        'mother_id' => null,
        'father_id' => $father->id,
    ]);

    assertCount(3, $father->children);
    assertTrue($father->children->contains($child));
});

test('year getters work', function () {
    $personWithDates = factory(Person::class)->state('dead')->create([
        'birth_date_from' => '1957-05-20',
        'birth_date_to' => '1957-05-20',
        'death_date_from' => '2020-11-09',
        'death_date_to' => '2020-11-09',
    ]);
    $personWithSomeDates = factory(Person::class)->state('dead')->create([
        'birth_date_from' => '1893-01-01',
        'birth_date_to' => '1893-12-31',
        'death_date_from' => '1944-08-01',
        'death_date_to' => '1944-08-31',
    ]);
    $personWithoutDates = factory(Person::class)->create([
        'birth_date_from' => null,
        'birth_date_to' => null,
        'death_date_from' => null,
        'death_date_to' => null,
    ]);

    assertEquals(1957, $personWithDates->birth_year);
    assertEquals(2020, $personWithDates->death_year);

    assertEquals(1893, $personWithSomeDates->birth_year);
    assertEquals(1944, $personWithSomeDates->death_year);

    assertNull($personWithoutDates->birth_year);
    assertNull($personWithoutDates->death_year);
});

it('returns null when calculating age without date', function () {
    $person = factory(Person::class)->create([
        'birth_date_from' => null,
        'birth_date_to' => null,
    ]);

    $at = Carbon::create(2019, 8, 15);

    assertNull($person->age($at, true));
    assertNull($person->age($at));
});

it('can calculate age with complete dates', function () {
    $person = factory(Person::class)->create([
        'birth_date_from' => '1994-06-22',
        'birth_date_to' => '1994-06-22',
    ]);

    $at = Carbon::create(2019, 8, 15);

    assertEquals(25, $person->age($at, true));
    assertEquals(25, $person->age($at));
});

it('can calculate age with incomplete birth date', function () {
    $person_without_day = factory(Person::class)->create([
        'birth_date_from' => '1978-04-01',
        'birth_date_to' => '1978-04-30',
    ]);

    $person_without_month = factory(Person::class)->create([
        'birth_date_from' => '1982-01-01',
        'birth_date_to' => '1982-12-31',
    ]);

    $at_diffrent_month = Carbon::create(2017, 6, 15);

    $at_same_month = Carbon::create(2006, 4, 16);

    assertEquals(39, $person_without_day->age($at_diffrent_month, true));
    assertEquals(39, $person_without_day->age($at_diffrent_month));
    assertEquals(28, $person_without_day->age($at_same_month, true)); // 27-28
    assertEquals('27-28', $person_without_day->age($at_same_month));
    assertEquals(35, $person_without_month->age($at_diffrent_month, true)); // 34-35
    assertEquals('34-35', $person_without_month->age($at_diffrent_month));
});

it('can calculate age with incomplete at date', function () {
    $person = factory(Person::class)->create([
        'birth_date_from' => '1975-03-22',
        'birth_date_to' => '1975-03-22',
    ]);

    $without_day = [Carbon::create(2013, 7, 01), Carbon::create(2013, 7, 31)];

    $without_day_in_same_month = [Carbon::create(2015, 3, 01), Carbon::create(2015, 3, 31)];

    $without_month = [Carbon::create(2016, 01, 01), Carbon::create(2016, 12, 31)];

    assertEquals(38, $person->age($without_day, true));
    assertEquals(38, $person->age($without_day));
    assertEquals(40, $person->age($without_day_in_same_month, true)); // 39-40
    assertEquals('39-40', $person->age($without_day_in_same_month));
    assertEquals(41, $person->age($without_month, true)); // 40-41
    assertEquals('40-41', $person->age($without_month));
});

it('can calculate age with incomplete dates', function () {
    $person = factory(Person::class)->create([
        'birth_date_from' => '1992-01-01',
        'birth_date_to' => '1992-12-31',
    ]);

    $at = [Carbon::create(2010, 7, 01), Carbon::create(2010, 7, 31)];

    assertEquals(18, $person->age($at, true)); // 17-18
    assertEquals('17-18', $person->age($at));
});

it('can calculate current age', function () {
    $person = factory(Person::class)->create([
        'birth_date_from' => '1973-05-12',
        'birth_date_to' => '1973-05-12',
    ]);

    travel('2016-11-10');

    assertEquals('2016-11-10', Carbon::now()->format('Y-m-d'));
    assertEquals(43, $person->currentAge(true));
    assertEquals(43, $person->currentAge());

    travel('back');
});

it('can calculate age age at death', function () {
    $person = factory(Person::class)->create([
        'birth_date_from' => '1874-04-08',
        'birth_date_to' => '1874-04-08',
        'death_date_from' => '1941-05-30',
        'death_date_to' => '1941-05-30',
    ]);

    assertEquals(67, $person->ageAtDeath(true));
    assertEquals(67, $person->ageAtDeath());
});

it('can format name', function () {
    $person = factory(Person::class)->state('alive')->create([
        'name' => 'Zenona',
        'middle_name' => 'Ludmiła',
        'family_name' => 'Skwierczyńska',
        'last_name' => null,
        'birth_date_from' => null,
        'birth_date_to' => null,
    ]);

    assertEquals("Zenona Skwierczyńska [№$person->id]", $person->formatName());

    $person->last_name = 'Wojtyła';
    assertEquals("Zenona Wojtyła (Skwierczyńska) [№$person->id]", $person->formatName());
    $person->last_name = null;

    $person->birth_date_from = '1913-05-01';
    $person->birth_date_to = '1913-05-01';
    assertEquals("Zenona Skwierczyńska (∗1913) [№$person->id]", $person->formatName());

    $person->dead = true;
    $person->death_date_from = '1945-01-01';
    $person->death_date_to = '1945-12-31';
    assertEquals("Zenona Skwierczyńska (∗1913, ✝1945) [№$person->id]", $person->formatName());

    $person->birth_date_from = null;
    $person->birth_date_to = null;
    assertEquals("Zenona Skwierczyńska (✝1945) [№$person->id]", $person->formatName());
});

it('can format simple name', function () {
    $person = factory(Person::class)->state('alive')->create([
        'name' => 'Zenona',
        'middle_name' => 'Ludmiła',
        'family_name' => 'Skwierczyńska',
        'last_name' => null,
    ]);

    assertEquals('Zenona Skwierczyńska', $person->formatSimpleName());

    $person->last_name = 'Wojtyła';

    assertEquals('Zenona Wojtyła (Skwierczyńska)', $person->formatSimpleName());
});

it('casts sources to collection', function () {
    $sources = factory(Person::class)->create([
        'sources' => null,
    ])->sources;

    assertInstanceOf(Collection::class, $sources);
    assertTrue($sources->isEmpty());

    $sources = factory(Person::class)->create([
        'sources' => [],
    ])->sources;

    assertInstanceOf(Collection::class, $sources);
    assertTrue($sources->isEmpty());

    $sources = factory(Person::class)->create([
        'sources' => [
            '[Henryk Gąsiorowski](https://pl.wikipedia.org/wiki/Henryk_G%C4%85siorowski) w Wikipedii, wolnej encyklopedii, dostęp 2020-06-06',
            'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
        ],
    ])->sources;

    assertInstanceOf(Collection::class, $sources);
    assertCount(2, $sources);
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

    assertEquals(
        $sanitized,
        factory(Person::class)->create(['sources' => $unsanitized])
            ->sources->map->raw()->all()
    );
});

it('can be found by pytlewski id', function () {
    $person = factory(Person::class)->create([
        'id_pytlewski' => 1140,
    ]);

    assertTrue($person->is(Person::findByPytlewskiId(1140)));
    assertNull(Person::findByPytlewskiId(null));
    assertNull(Person::findByPytlewskiId(2137));
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
        factory(Person::class)->create($names);
    });

    assertEquals(
        [
            ['letter' => 'M', 'total' => 2],
            ['letter' => 'Š', 'total' => 1],
            ['letter' => 'Ż', 'total' => 1],
        ],
        Person::letters('family')->map(fn ($std) => (array) $std)->toArray()
    );

    assertEquals(
        [
            ['letter' => 'H', 'total' => 1],
            ['letter' => 'M', 'total' => 1],
            ['letter' => 'Š', 'total' => 2],
        ],
        Person::letters('last')->map(fn ($std) => (array) $std)->toArray()
    );
});
