<?php

use App\Models\Marriage;
use App\Models\Person;
use function Pest\Laravel\post;
use function Pest\Laravel\travel;
use function Pest\Laravel\travelBack;

beforeEach(function () {
    $this->dates = [
        'first_event_date_from', 'second_event_date_from', 'divorce_date_from',
        'first_event_date_to', 'second_event_date_to', 'divorce_date_to',
    ];

    $this->enums = ['rite', 'first_event_type', 'second_event_type'];

    $this->validAttributes = [
        'woman_id' => Person::factory()->woman()->create()->id,
        'woman_order' => 1,
        'man_id' => Person::factory()->man()->create()->id,
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
        'divorced' => true,
        'divorce_date_from' => '2001-10-27',
        'divorce_date_to' => '2001-10-27',
        'divorce_place' => 'Toruń, Polska',
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

    expect(Marriage::count())->toBe($count);
});

test('users without permissions cannot add valid marriage', function () {
    $count = Marriage::count();

    withPermissions(1)
        ->post('marriages', $this->validAttributes)
        ->assertStatus(403);

    expect(Marriage::count())->toBe($count);
});

test('users with permissions can add valid marriage', function () {
    $count = Marriage::count();

    travel(5)->minutes();

    withPermissions(2)
        ->post('marriages', $this->validAttributes)
        ->assertStatus(302)
        ->assertRedirect('people/'.Marriage::latest()->first()->woman_id);

    travelBack();

    expect(Marriage::count())->toBe($count + 1);

    $marriage = Marriage::latest()->first();

    $attributesToCheck = Arr::except($this->validAttributes, array_merge($this->dates, $this->enums));

    foreach ($attributesToCheck as $key => $attribute) {
        expect($marriage->$key)->toBe($attribute);
    }

    foreach ($this->enums as $enum) {
        expect((string) $marriage->$enum)->toBe($this->validAttributes[$enum]);
    }

    foreach ($this->dates as $date) {
        expect($marriage->$date->toDateString())->toBe($this->validAttributes[$date]);
    }
});

test('user can pass spouse to form by get request parameters', function () {
    $woman = Person::factory()->woman()->create();
    $man = Person::factory()->man()->create();

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

    expect(Marriage::count())->toBe($count + 1);

    $marriage = Marriage::latest()->first();

    $log = latestLog();

    expect($log->log_name)->toBe('marriages');
    expect($log->description)->toBe('created');
    expect($marriage->is($log->subject))->toBeTrue();

    $attributesToCheck = Arr::except($this->validAttributes, $this->dates);

    foreach ($attributesToCheck as $key => $value) {
        expect($log->properties['attributes'][$key])->toBe($value);
        // 'Failed asserting that attribute '.$key.' has the same value in log.'
    }

    expect((string) $log->created_at)->toBe((string) $marriage->created_at);
    expect((string) $log->created_at)->toBe((string) $marriage->updated_at);

    expect($log->properties['attributes'])->not->toHaveKey('created_at');
    expect($log->properties['attributes'])->not->toHaveKey('updated_at');

    foreach ($this->dates as $date) {
        expect($log->properties['attributes'][$date])
            ->toBe($marriage->$date->format('Y-m-d'));
    }
});
