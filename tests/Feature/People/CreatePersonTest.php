<?php

use App\Models\Person;
use Illuminate\Support\Arr;
use function Pest\Laravel\post;
use function Pest\Laravel\travel;
use function Pest\Laravel\travelBack;

beforeEach(function () {
    $this->dates = [
        'birth_date_from', 'death_date_from', 'funeral_date_from', 'burial_date_from',
        'birth_date_to', 'death_date_to', 'funeral_date_to', 'burial_date_to',
    ];

    $this->validAttributes = [
        'id_wielcy' => 'psb.6305.1',
        'id_pytlewski' => 2137,
        'sex' => 'xy',
        'name' => 'Henryk',
        'middle_name' => 'Erazm',
        'family_name' => 'Gąsiorowski',
        'last_name' => 'Jakże to',
        'birth_date_from' => '1878-04-01',
        'birth_date_to' => '1878-04-01',
        'birth_place' => 'Zaleszczyki, Polska',
        'dead' => true,
        'death_date_from' => '1947-01-17',
        'death_date_to' => '1947-01-17',
        'death_place' => 'Grudziądz, Polska',
        'death_cause' => 'rak',
        'funeral_date_from' => '1947-01-21',
        'funeral_date_to' => '1947-01-21',
        'funeral_place' => 'Grudziądz, Polska',
        'burial_date_from' => '1947-01-21',
        'burial_date_to' => '1947-01-21',
        'burial_place' => 'Grudziądz, Polska',
        'mother_id' => Person::factory()->woman()->create()->id,
        'father_id' => Person::factory()->man()->create()->id,
        'sources' => [
            '[Henryk Gąsiorowski](https://pl.wikipedia.org/wiki/Henryk_G%C4%85siorowski) w Wikipedii, wolnej encyklopedii, dostęp 2020-06-06',
            'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
        ],
    ];
});

test('guest are asked to log in when attempting to view add person form')
    ->get('people/create')
    ->assertStatus(302)
    ->assertRedirect('login');

test('users without permissions cannot view add person form')
    ->withPermissions(1)
    ->get('people/create')
    ->assertStatus(403);

test('users with permissions can view add person form')
    ->withPermissions(2)
    ->get('people/create')
    ->assertStatus(200);

test('guest cannot add valid person', function () {
    $count = Person::count();

    post('people', $this->validAttributes)
        ->assertStatus(302)
        ->assertRedirect('login');

    expect(Person::count())->toBe($count);
});

test('users without permissions cannot add valid person', function () {
    $count = Person::count();

    withPermissions(1)
        ->post('people', $this->validAttributes)
        ->assertStatus(403);

    expect(Person::count())->toBe($count);
});

test('users with permissions can add valid person', function () {
    $count = Person::count();

    travel(5)->minutes();

    withPermissions(2)
        ->post('people', $this->validAttributes)
        ->assertStatus(302)
        ->assertRedirect('people/'.Person::latest()->first()->id);

    travelBack();

    expect(Person::count())->toBe($count + 1);

    $person = Person::latest()->first();

    $attributesToCheck = Arr::except($this->validAttributes, [
        'sources', ...$this->dates,
    ]);

    foreach ($attributesToCheck as $key => $attribute) {
        expect($person->{$key})->toBe($attribute);
    }

    expect($person->sources)->toHaveCount(2);
    expect($person->sources->map->raw()->all())
        ->toBe($this->validAttributes['sources']);

    foreach ($this->dates as $date) {
        expect($person->{$date}->toDateString())->toBe($this->validAttributes[$date]);
    }
});

test('you can pass parents ids to form by get request parameters', function () {
    $mother = Person::factory()->woman()->create();
    $father = Person::factory()->man()->create();

    withPermissions(2)
        ->get("people/create?mother={$mother->id}&father={$father->id}")
        ->assertStatus(200)
        ->assertSee($mother->id)
        ->assertSee($father->id);
});

test('data is validated using appropriate form request')
    ->assertActionUsesFormRequest(
        \App\Http\Controllers\PersonController::class, 'store',
        \App\Http\Requests\StorePerson::class
    );

test('person creation is logged', function () {
    $count = Person::count();

    travel(5)->minutes();

    withPermissions(2)
        ->post('people', $this->validAttributes);

    travelBack();

    expect(Person::count())->toBe($count + 1);

    $person = Person::latest()->first();

    $log = latestLog();

    expect($log->log_name)->toBe('people');
    expect($log->description)->toBe('created');
    expect($log->subject())->toBeModel($person);

    $attributesToCheck = Arr::except($this->validAttributes, [
        'sources', ...$this->dates,
    ]);

    foreach ($attributesToCheck as $key => $value) {
        expect($log->properties['attributes'][$key])->toBe($value);
        // 'Failed asserting that attribute '.$key.' has the same value in log.'
    }

    expect((string) $log->created_at)->toBe((string) $person->created_at);
    expect((string) $log->created_at)->toBe((string) $person->updated_at);

    expect($log->properties['attributes'])->not->toHaveKey('created_at');
    expect($log->properties['attributes'])->not->toHaveKey('updated_at');

    expect($log->properties['attributes']['sources'])->toHaveCount(2);
    expect($log->properties['attributes']['sources'])
        ->toBe($this->validAttributes['sources']);

    foreach ($this->dates as $date) {
        expect($log->properties['attributes'][$date])
            ->toBe($person->{$date}->format('Y-m-d'));
    }
});
