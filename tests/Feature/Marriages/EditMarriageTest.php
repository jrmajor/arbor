<?php

use App\Models\Marriage;
use App\Models\Person;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;
use function Pest\Laravel\put;
use function Pest\Laravel\travel;
use function Pest\Laravel\travelBack;
use function Tests\latestLog;
use function Tests\withPermissions;

beforeEach(function () {
    $this->dates = [
        'first_event_date_from', 'second_event_date_from', 'divorce_date_from',
        'first_event_date_to', 'second_event_date_to', 'divorce_date_to',
    ];

    $this->enums = ['rite', 'first_event_type', 'second_event_type'];

    $this->oldAttributes = [
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
        'divorced' => false,
        'divorce_date_from' => null,
        'divorce_date_to' => null,
        'divorce_place' => null,
    ];

    $this->newAttributes = [
        'woman_id' => Person::factory()->woman()->create()->id,
        'woman_order' => 2,
        'man_id' => Person::factory()->man()->create()->id,
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
        'divorced' => true,
        'divorce_date_from' => '2001-10-27',
        'divorce_date_to' => '2001-10-27',
        'divorce_place' => 'Toruń, Polska',
    ];

    $this->marriage = Marriage::factory()->create($this->oldAttributes);
});

test('guests are asked to log in when attempting to view edit marriage form', function () {
    get("marriages/{$this->marriage->id}/edit")
        ->assertStatus(302)
        ->assertRedirect('login');
});

test('guests are asked to log in when attempting to view edit form for nonexistent marriage')
    ->get('marriages/2137/edit')
    ->assertStatus(302)
    ->assertRedirect('login');

test('users without permissions cannot view edit marriage form', function () {
    withPermissions(1)
        ->get("marriages/{$this->marriage->id}/edit")
        ->assertStatus(403);
});

test('users with permissions can view edit marriage form', function () {
    withPermissions(2)
        ->get("marriages/{$this->marriage->id}/edit")
        ->assertStatus(200);
});

test('guests cannot edit marriage', function () {
    put("marriages/{$this->marriage->id}", $this->newAttributes)
        ->assertStatus(302)
        ->assertRedirect('login');

    $this->marriage->refresh();

    $attributesToCheck = Arr::except($this->oldAttributes, array_merge($this->dates, $this->enums));

    foreach ($attributesToCheck as $key => $attribute) {
        expect($this->marriage->{$key})->toBe($attribute);
    }
});

test('users without permissions cannot edit marriage', function () {
    withPermissions(1)
        ->put("marriages/{$this->marriage->id}", $this->newAttributes)
        ->assertStatus(403);

    $this->marriage->refresh();

    $attributesToCheck = Arr::except($this->oldAttributes, array_merge($this->dates, $this->enums));

    foreach ($attributesToCheck as $key => $attribute) {
        expect($this->marriage->{$key})->toBe($attribute);
    }
});

test('users with permissions can edit marriage', function () {
    $response = withPermissions(2)
        ->put("marriages/{$this->marriage->id}", $this->newAttributes)
        ->assertStatus(302);

    $this->marriage->refresh();

    $response->assertRedirect('people/' . $this->marriage->woman_id);

    $attributesToCheck = Arr::except($this->newAttributes, array_merge($this->dates, $this->enums));

    foreach ($attributesToCheck as $key => $attribute) {
        expect($this->marriage->{$key})->toBe($attribute);
    }

    foreach ($this->enums as $enum) {
        expect((string) $this->marriage->{$enum})->toBe($this->newAttributes[$enum]);
    }

    foreach ($this->dates as $date) {
        expect($this->marriage->{$date}->toDateString())
            ->toBe($this->newAttributes[$date]);
    }
});

test('data is validated using appropriate form request')
    ->assertActionUsesFormRequest(
        [App\Http\Controllers\MarriageController::class, 'update'],
        App\Http\Requests\StoreMarriage::class,
    );

test('marriage edition is logged', function () {
    travel(5)->minutes();

    withPermissions(2)
        ->put("marriages/{$this->marriage->id}", $this->newAttributes);

    travelBack();

    $this->marriage->refresh();

    $log = latestLog();

    expect($log->log_name)->toBe('marriages');
    expect($log->description)->toBe('updated');
    expect($log->subject())->toBeModel($this->marriage);

    $oldToCheck = Arr::except($this->oldAttributes, $this->dates);

    foreach ($oldToCheck as $key => $value) {
        expect($log->properties['old'][$key])->toBe($value);
        // 'Failed asserting that old attribute '.$key.' has the same value in log.'
    }

    $newToCheck = Arr::except($this->newAttributes, $this->dates);

    foreach ($newToCheck as $key => $value) {
        expect($log->properties['attributes'][$key])->toBe($value);
        // 'Failed asserting that attribute '.$key.' has the same value in log.'
    }

    expect($log->properties['old'])->not->toHaveKey('created_at');
    expect($log->properties['attributes'])->not->toHaveKey('created_at');

    expect((string) $log->created_at)->toBe((string) $this->marriage->updated_at);

    expect($log->properties['old'])->not->toHaveKey('updated_at');
    expect($log->properties['attributes'])->not->toHaveKey('updated_at');

    foreach ($this->dates as $date) {
        expect($log->properties['old'][$date])
            ->toBe($this->oldAttributes[$date]);
        expect($log->properties['attributes'][$date])
            ->toBe($this->newAttributes[$date]);
    }
});
