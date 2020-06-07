<?php

use App\Marriage;
use App\Person;

beforeEach(function () {
    $this->dates = [
        'first_event_date_from', 'second_event_date_from', 'end_date_from',
        'first_event_date_to', 'second_event_date_to', 'end_date_to',
    ];

    $this->validAttributes = [
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
        'ended' => '1',
        'end_cause' => 'rozwód',
        'end_date_from' => '2001-10-27',
        'end_date_to' => '2001-10-27',
    ];
});

test('guest are asked to log in when attempting to view add marriage form')
    ->get('marriages/create')
    ->assertStatus(302)
    ->assertRedirect('login');

test('users without permissions cannot view add marriage form')
    ->withPermissions(1)
    ->get('marriages/create')
    ->assertStatus(403);

test('users with permissions can view add marriage form')
    ->withPermissions(2)
    ->get('marriages/create')
    ->assertStatus(200);

test('guest cannot add valid marriage', function () {
    $count = Marriage::count();

    post('marriages', $this->validAttributes)
        ->assertStatus(302)
        ->assertRedirect('login');

    assertEquals($count, Marriage::count());
});

test('users without permissions cannot add valid marriage', function () {
    $count = Marriage::count();

    withPermissions(1)
        ->post('marriages', $this->validAttributes)
        ->assertStatus(403);

    assertEquals($count, Marriage::count());
});

test('users with permissions can add valid marriage', function () {
    $count = Marriage::count();

    travel('+5 minutes');

    withPermissions(2)
        ->post('marriages', $this->validAttributes)
        ->assertStatus(302)
        ->assertRedirect('people/'.Marriage::latest()->first()->woman_id);

    travel('back');

    assertEquals($count + 1, Marriage::count());

    $marriage = Marriage::latest()->first();

    $attributesToCheck = Arr::except($this->validAttributes, $this->dates);

    foreach ($attributesToCheck as $key => $attribute) {
        assertEquals($attribute, $marriage->$key);
    }

    foreach ($this->dates as $date) {
        assertTrue($this->validAttributes[$date] == $marriage->$date->toDateString());
    }
});

test('user can pass spouse to form by get request parameters', function () {
    $woman = factory(Person::class)->state('woman')->create();
    $man = factory(Person::class)->state('man')->create();

    withPermissions(2)
        ->get("marriages/create?woman=$woman->id&man=$man->id")
        ->assertStatus(200)
        ->assertSee($woman->id)
        ->assertSee($man->id);
});

test('data is validated using appropriate form request')
    ->assertActionUsesFormRequest(
        \App\Http\Controllers\MarriageController::class, 'store',
        \App\Http\Requests\StoreMarriage::class
    );

test('marriage creation is logged', function () {
    $count = Marriage::count();

    travel('+5 minutes');

    withPermissions(2)
        ->post('marriages', $this->validAttributes);

    travel('back');

    assertEquals($count + 1, Marriage::count());

    $marriage = Marriage::latest()->first();

    $log = latestLog();

    assertEquals('marriages', $log->log_name);
    assertEquals('created', $log->description);
    assertTrue($marriage->is($log->subject));

    $attributesToCheck = Arr::except($this->validAttributes, $this->dates);

    foreach ($attributesToCheck as $key => $value) {
        assertEquals(
            $value, $log->properties['attributes'][$key],
            'Failed asserting that attribute '.$key.' has the same value in log.'
        );
    }

    assertEquals($marriage->created_at, $log->created_at);
    assertEquals($marriage->updated_at, $log->created_at);

    assertArrayNotHasKey('created_at', $log->properties['attributes']);
    assertArrayNotHasKey('updated_at', $log->properties['attributes']);

    foreach ($this->dates as $date) {
        assertEquals($marriage->$date->format('Y-m-d'), $log->properties['attributes'][$date]);
    }
});
