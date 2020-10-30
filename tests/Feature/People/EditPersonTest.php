<?php

use App\Models\Person;
use function Pest\Laravel\get;
use function Pest\Laravel\put;
use function Pest\Laravel\travel;
use function Pest\Laravel\travelBack;

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
        'sources' => [
            '[Henryk Gąsiorowski](https://pl.wikipedia.org/wiki/Henryk_G%C4%85siorowski) w Wikipedii, wolnej encyklopedii, dostęp 2020-06-06',
            'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
        ],
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
        'sources' => [
            'Witold Jakóbczyk, *Czesław Czypicki* w Wielkopolskim słowniku biograficznym, Warszawa-Poznań, PWN, 1981, ISBN 83-01-02722-3',
            'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
        ],
    ];

    $this->person = Person::factory()->create($this->oldAttributes);
});

test('guests are asked to log in when attempting to view edit person form', function () {
    get("people/{$this->person->id}/edit")
        ->assertStatus(302)
        ->assertRedirect('login');
});

test('guests are asked to log in when attempting to view edit form for nonexistent person')
    ->get('people/2137/edit')
    ->assertStatus(302)
    ->assertRedirect('login');

test('users without permissions cannot view edit person form', function () {
    withPermissions(1)
        ->get("people/{$this->person->id}/edit")
        ->assertStatus(403);
});

test('users with permissions can view edit person form', function () {
    withPermissions(2)
        ->get("people/{$this->person->id}/edit")
        ->assertStatus(200);
});

test('guests cannot edit person', function () {
    put("people/{$this->person->id}", $this->newAttributes)
        ->assertStatus(302)
        ->assertRedirect('login');

    $this->person->refresh();

    $attributesToCheck = Arr::except($this->oldAttributes, [
        'sources', ...$this->dates,
    ]);

    foreach ($attributesToCheck as $key => $attribute) {
        expect($this->person->$key)->toBe($attribute);
    }
});

test('users without permissions cannot edit person', function () {
    withPermissions(1)
        ->put("people/{$this->person->id}", $this->newAttributes)
        ->assertStatus(403);

    $this->person->refresh();

    $attributesToCheck = Arr::except($this->oldAttributes, [
        'sources', ...$this->dates,
    ]);

    foreach ($attributesToCheck as $key => $attribute) {
        expect($this->person->$key)->toBe($attribute);
    }
});

test('users with permissions can edit person', function () {
    withPermissions(2)
        ->put("people/{$this->person->id}", $this->newAttributes)
        ->assertStatus(302)
        ->assertRedirect("people/{$this->person->id}");

    $this->person->refresh();

    $attributesToCheck = Arr::except($this->newAttributes, [
        'sources', ...$this->dates,
    ]);

    foreach ($attributesToCheck as $key => $attribute) {
        expect($this->person->$key)->toBe($attribute);
    }

    expect($this->person->sources)->toHaveCount(2);
    expect($this->person->sources->map->raw()->all())
        ->toBe($this->newAttributes['sources']);

    foreach ($this->dates as $date) {
        expect($this->person->$date->toDateString())->toBe($this->newAttributes[$date]);
    }
});

test('data is validated using appropriate form request')
    ->assertActionUsesFormRequest(
        \App\Http\Controllers\PersonController::class, 'update',
        \App\Http\Requests\StorePerson::class
    );

test('person edition is logged', function () {
    travel(5)->minutes();

    withPermissions(2)
        ->put("people/{$this->person->id}", $this->newAttributes);

    travelBack();

    $this->person->refresh();

    $log = latestLog();

    expect($log->log_name)->toBe('people');
    expect($log->description)->toBe('updated');
    expect($log->subject()->is($this->person))->toBeTrue();

    $oldToCheck = Arr::except($this->oldAttributes, [
        'dead', 'death_cause', 'sources', ...$this->dates,
    ]);

    foreach ($oldToCheck as $key => $value) {
        expect($log->properties['old'][$key])->toBe($value);
        // 'Failed asserting that old attribute '.$key.' has the same value in log.'
    }

    $newToCheck = Arr::except($this->newAttributes, [
        'dead', 'death_cause', 'sources', ...$this->dates,
    ]);

    foreach ($newToCheck as $key => $value) {
        expect($log->properties['attributes'][$key])->toBe($value);
        // 'Failed asserting that attribute '.$key.' has the same value in log.'
    }

    expect($log->properties['old'])->not->toHaveKey('dead');
    expect($log->properties['attributes'])->not->toHaveKey('dead');

    expect($log->properties['old'])->not->toHaveKey('death_cause');
    expect($log->properties['attributes'])->not->toHaveKey('death_cause');

    expect($log->properties['old'])->not->toHaveKey('created_at');
    expect($log->properties['attributes'])->not->toHaveKey('created_at');

    expect((string) $log->created_at)->toBe((string) $this->person->updated_at);

    expect($log->properties['old'])->not->toHaveKey('updated_at');
    expect($log->properties['attributes'])->not->toHaveKey('updated_at');

    expect($log->properties['old']['sources'])->toHaveCount(2);
    expect($log->properties['attributes']['sources'])->toHaveCount(2);

    expect($log->properties['old']['sources'])
        ->toBe($this->oldAttributes['sources']);
    expect($log->properties['attributes']['sources'])
        ->toBe($this->newAttributes['sources']);

    foreach ($this->dates as $date) {
        expect($log->properties['old'][$date])->toBe($this->oldAttributes[$date]);
        expect($log->properties['attributes'][$date])->toBe($this->newAttributes[$date]);
    }
});
