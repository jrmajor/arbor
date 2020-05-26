<?php

use App\Marriage;
use App\Person;
use Illuminate\Support\Arr;

beforeEach(function () {
    $this->dates = [
        'first_event_date_from', 'second_event_date_from', 'end_date_from',
        'first_event_date_to', 'second_event_date_to', 'end_date_to',
    ];

    $this->oldAttributes = [
        'woman_id' => factory(Person::class)->state('woman')->create()->id,
        'woman_order' => 1,
        'man_id' => factory(Person::class)->state('man')->create()->id,
        'man_order' => 2,
        'rite' => 'roman_catholic',
        'first_event_type' => 'civil_marriage',
        'first_event_date_from' => '1968-04-14',
        'first_event_date_to' => '1968-04-14',
        'first_event_place' => 'Sępólno Krajeńskie, Polska',
        'second_event_type' => 'church_marriage',
        'second_event_date_from' => '1968-04-13',
        'second_event_date_to' => '1968-04-13',
        'second_event_place' => 'Sępólno Krajeńskie, Polska',
        'ended' => true,
        'end_cause' => 'rozwód',
        'end_date_from' => '2001-10-27',
        'end_date_to' => '2001-10-27',
    ];

    $this->newAttributes = [
        'woman_id' => factory(Person::class)->state('woman')->create()->id,
        'woman_order' => 2,
        'man_id' => factory(Person::class)->state('man')->create()->id,
        'man_order' => 1,
        'rite' => 'civil',
        'first_event_type' => 'concordat_marriage',
        'first_event_date_from' => '1960-09-02',
        'first_event_date_to' => '1968-04-14',
        'first_event_place' => 'Warszawa, Polska',
        'second_event_type' => 'civil_marriage',
        'second_event_date_from' => '1960-09-05',
        'second_event_date_to' => '1960-09-05',
        'second_event_place' => 'Warszawa, Polska',
        'ended' => false,
        'end_cause' => 'bo tak',
        'end_date_from' => '2000-03-27',
        'end_date_to' => '2000-03-27',
    ];

    $this->marriage = factory(Marriage::class)->create($this->oldAttributes);
});

test('guests are asked to log in when attempting to view edit marriage form', function () {
    get('marriages/'.$this->marriage->id.'/edit')
        ->assertStatus(302)
        ->assertRedirect('login');
});

test('guests are asked to log in when attempting to view edit form for nonexistent marriage')
    ->get('marriages/2137/edit')
    ->assertStatus(302)
    ->assertRedirect('login');

test('users without permissions cannot view edit marriage form', function () {
    withPermissions(1)
        ->get('marriages/'.$this->marriage->id.'/edit')
        ->assertStatus(403);
});

test('users with permissions can view edit marriage form', function () {
    withPermissions(2)
        ->get('marriages/'.$this->marriage->id.'/edit')
        ->assertStatus(200);
});

test('guests cannot edit marriage', function () {
    put('marriages/'.$this->marriage->id, $this->newAttributes)
        ->assertStatus(302)
        ->assertRedirect('login');

    $attributesToCheck = Arr::except($this->oldAttributes, [
        'first_event_date_from', 'second_event_date_from', 'end_date_from',
        'first_event_date_to', 'second_event_date_to', 'end_date_to',
    ]);

    $this->marriage = $this->marriage->fresh();

    foreach ($attributesToCheck as $key => $attribute) {
        assertEquals($attribute, $this->marriage->$key);
    }
});

test('users without permissions cannot edit marriage', function () {
    withPermissions(1)
        ->put('marriages/'.$this->marriage->id, $this->newAttributes)
        ->assertStatus(403);

    $this->marriage = $this->marriage->fresh();

    foreach (Arr::except($this->oldAttributes, $this->dates) as $key => $attribute) {
        assertEquals($attribute, $this->marriage->$key);
    }
});

test('users with permissions can edit marriage', function () {
    $response = withPermissions(2)
        ->put('marriages/'.$this->marriage->id, $this->newAttributes)
        ->assertStatus(302);

    $this->marriage = $this->marriage->fresh();

    $response->assertRedirect('people/'.$this->marriage->woman_id);

    foreach (Arr::except($this->newAttributes, $this->dates) as $key => $attribute) {
        assertEquals($attribute, $this->marriage->$key);
    }

    foreach ($this->dates as $date) {
        assertEquals($this->newAttributes[$date], $this->marriage->$date->toDateString());
    }
});

test('data is validated using appropriate form request')
    ->assertActionUsesFormRequest(
        \App\Http\Controllers\MarriageController::class, 'update',
        \App\Http\Requests\StoreMarriage::class
    );

test('marriage edition is logged', function () {
    travel(
        '+1 minute',
        fn () => $this->marriage->fill($this->newAttributes)->save()
    );

    $this->marriage = $this->marriage->fresh();

    $log = latestLog();

    assertEquals('marriages', $log->log_name);
    assertEquals('updated', $log->description);
    assertTrue($this->marriage->is($log->subject));

    $oldToCheck = Arr::except($this->oldAttributes, [
        'id', 'created_at', 'updated_at',
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
        ...$this->dates,
    ]);

    foreach ($attributesToCheck as $key => $value) {
        assertEquals(
            $value, $log->properties['attributes'][$key],
            'Failed asserting that attribute '.$key.' has the same value in log.'
        );
    }

    assertArrayNotHasKey('created_at', $log->properties['old']);
    assertArrayNotHasKey('created_at', $log->properties['attributes']);

    assertEquals($this->marriage->updated_at, $log->created_at);

    assertArrayNotHasKey('updated_at', $log->properties['old']);
    assertArrayNotHasKey('updated_at', $log->properties['attributes']);

    foreach ($this->dates as $date) {
        assertEquals($this->oldAttributes[$date], $log->properties['old'][$date]);
        assertEquals($this->newAttributes[$date], $log->properties['attributes'][$date]);
    }
});
