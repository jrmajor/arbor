<?php

use App\Person;

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
        'mother_id' => factory(Person::class)->state('woman')->create()->id,
        'father_id' => factory(Person::class)->state('man')->create()->id,
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

    assertEquals($count, Person::count());
});

test('users without permissions cannot add valid person', function () {
    $count = Person::count();

    withPermissions(1)
        ->post('people', $this->validAttributes)
        ->assertStatus(403);

    assertEquals($count, Person::count());
});

test('users with permissions can add valid person', function () {
    $count = Person::count();

    travel('+5 minutes');

    withPermissions(2)
        ->post('people', $this->validAttributes)
        ->assertStatus(302)
        ->assertRedirect('people/'.Person::latest()->first()->id);

    assertEquals($count + 1, Person::count());

    $person = Person::latest()->first();

    foreach (Arr::except($this->validAttributes, $this->dates) as $key => $attribute) {
        assertEquals($attribute, $person->$key);
    }

    foreach ($this->dates as $date) {
        assertTrue($this->validAttributes[$date] == $person->$date->toDateString());
    }
});

test('you can pass parents ids to form by get request parameters', function () {
    $mother = factory(Person::class)->state('woman')->create();
    $father = factory(Person::class)->state('man')->create();

    withPermissions(2)
        ->get("people/create?mother=$mother->id&father=$father->id")
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
    $person = factory(Person::class)->state('dead')->create();

    $log = latestLog();

    assertEquals('people', $log->log_name);
    assertEquals('created', $log->description);
    assertTrue($person->is($log->subject));

    $attributesToCheck = Arr::except($person->getAttributes(), [
        'id', 'created_at', 'updated_at',
        ...$this->dates,
    ]);

    foreach ($attributesToCheck as $key => $value) {
        assertEquals(
            $value, $log->properties['attributes'][$key],
            'Failed asserting that attribute '.$key.' has the same value in log.'
        );
    }

    assertEquals($person->created_at, $log->created_at);
    assertEquals($person->updated_at, $log->created_at);

    assertArrayNotHasKey('created_at', $log->properties['attributes']);
    assertArrayNotHasKey('updated_at', $log->properties['attributes']);

    foreach ($this->dates as $date) {
        assertEquals($person->$date->format('Y-m-d'), $log->properties['attributes'][$date]);
    }
});
