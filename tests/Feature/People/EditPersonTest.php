<?php

use App\Person;

beforeEach(function () {
    $this->dates = [
        'birth_date_from', 'death_date_from', 'funeral_date_from', 'burial_date_from',
        'birth_date_to', 'death_date_to', 'funeral_date_to', 'burial_date_to',
    ];

    $this->oldAttributes = [
        'id_wielcy' => 'psb.6305.3',
        'id_pytlewski' => 2115,
        'sex' => 'xx',
        'name' => 'Maria',
        'middle_name' => 'Henryka',
        'family_name' => 'Stecher de Sebenitz',
        'last_name' => 'Gąsiorowska',
        'birth_date_from' => '1854-01-12',
        'birth_date_to' => '1854-01-12',
        'birth_place' => 'Zaleszczyki, Ukraina',
        'dead' => true,
        'death_date_from' => '1918-01-02',
        'death_date_to' => '1918-01-02',
        'death_place' => 'Załuż k/Sanoka, Polska',
        'death_cause' => 'rak',
        'funeral_date_from' => '1918-01-05',
        'funeral_date_to' => '1918-01-05',
        'funeral_place' => 'Załuż k/Sanoka, Polska',
        'burial_date_from' => '1918-01-05',
        'burial_date_to' => '1918-01-05',
        'burial_place' => 'Załuż k/Sanoka, Polska',
    ];

    $this->newAttributes = [
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
    ];

    $this->person = factory(Person::class)->create($this->oldAttributes);
});

test('guests are asked to log in when attempting to view edit person form', function () {
    get('people/'.$this->person->id.'/edit')
        ->assertStatus(302)
        ->assertRedirect('login');
});

test('guests are asked to log in when attempting to view edit form for nonexistent person')
    ->get('people/2137/edit')
    ->assertStatus(302)
    ->assertRedirect('login');

test('users without permissions cannot view edit person form', function () {
    withPermissions(1)
        ->get('people/'.$this->person->id.'/edit')
        ->assertStatus(403);
});

test('users with permissions can view edit person form', function () {
    withPermissions(2)
        ->get('people/'.$this->person->id.'/edit')
        ->assertStatus(200);
});

test('guests cannot edit person', function () {
    put('people/'.$this->person->id, $this->newAttributes)
        ->assertStatus(302)
        ->assertRedirect('login');

    $this->person = $this->person->fresh();

    foreach (Arr::except($this->oldAttributes, $this->dates) as $key => $attribute) {
        assertEquals($attribute, $this->person->$key);
    }
});

test('users without permissions cannot edit person', function () {
    withPermissions(1)
        ->put('people/'.$this->person->id, $this->newAttributes)
        ->assertStatus(403);

    $this->person = $this->person->fresh();

    foreach (Arr::except($this->oldAttributes, $this->dates) as $key => $attribute) {
        assertEquals($attribute, $this->person->$key);
    }
});

test('users with permissions can edit person', function () {
    withPermissions(2)
        ->put('people/'.$this->person->id, $this->newAttributes)
        ->assertStatus(302)
        ->assertRedirect('people/'.$this->person->id);

    $this->person = $this->person->fresh();

    foreach (Arr::except($this->newAttributes, $this->dates) as $key => $attribute) {
        assertEquals($attribute, $this->person->$key);
    }

    foreach ($this->dates as $date) {
        assertEquals($this->newAttributes[$date], $this->person->$date->toDateString());
    }
});

test('data is validated using appropriate form request')
    ->assertActionUsesFormRequest(
        \App\Http\Controllers\PersonController::class, 'update',
        \App\Http\Requests\StorePerson::class
    );

test('person edition is logged', function () {
    travel(
        '+1 minute',
        fn () => $this->person->fill($this->newAttributes)->save()
    );

    $this->person = $this->person->fresh();

    $log = latestLog();

    assertEquals('people', $log->log_name);
    assertEquals('updated', $log->description);
    assertTrue($this->person->is($log->subject));

    $oldToCheck = Arr::except($this->oldAttributes, [
        'id', 'created_at', 'updated_at',
        'dead', 'death_cause',
        ...$this->dates,
    ]);

    foreach ($oldToCheck as $key => $value) {
        assertEquals(
            $value, $log->properties['old'][$key],
            'Failed asserting that old attribute '.$key.' has the same value in log.'
        );
    }

    $attributesToCheck = Arr::except($this->newAttributes, [
        'id', 'created_at', 'updated_at',
        'dead', 'death_cause',
        ...$this->dates,
    ]);

    foreach ($attributesToCheck as $key => $value) {
        assertEquals(
            $value, $log->properties['attributes'][$key],
            'Failed asserting that attribute '.$key.' has the same value in log.'
        );
    }

    assertArrayNotHasKey('dead', $log->properties['old']);
    assertArrayNotHasKey('dead', $log->properties['attributes']);

    assertArrayNotHasKey('death_cause', $log->properties['old']);
    assertArrayNotHasKey('death_cause', $log->properties['attributes']);

    assertArrayNotHasKey('created_at', $log->properties['old']);
    assertArrayNotHasKey('created_at', $log->properties['attributes']);

    assertEquals($this->person->updated_at, $log->created_at);

    assertArrayNotHasKey('updated_at', $log->properties['old']);
    assertArrayNotHasKey('updated_at', $log->properties['attributes']);

    foreach ($this->dates as $date) {
        assertEquals($this->oldAttributes[$date], $log->properties['old'][$date]);
        assertEquals($this->newAttributes[$date], $log->properties['attributes'][$date]);
    }
});
