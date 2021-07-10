<?php

use App\Models\Person;

use function Pest\Laravel\delete;
use function Tests\latestLog;
use function Tests\withPermissions;

beforeEach(function () {
    $this->person = Person::factory()->create();
});

test('guests cannot delete person', function () {
    delete("people/{$this->person->id}")
        ->assertStatus(302)
        ->assertRedirect('login');

    expect($this->person->fresh()->trashed())->toBeFalse();
});

test('users without permissions cannot delete person', function () {
    withPermissions(1)
        ->delete("people/{$this->person->id}")
        ->assertStatus(403);

    expect($this->person->fresh()->trashed())->toBeFalse();
});

test('users with permissions can delete person', function () {
    withPermissions(2)
        ->delete("people/{$this->person->id}")
        ->assertStatus(302);

    expect($this->person->fresh()->trashed())->toBeTrue();
});

test('users without permissions to view history are redirected to people index', function () {
    withPermissions(2)
        ->delete("people/{$this->person->id}")
        ->assertStatus(302)
        ->assertRedirect('people');
});

test('users with permissions to view history are redirected to person history', function () {
    withPermissions(3)
        ->delete("people/{$this->person->id}")
        ->assertStatus(302)
        ->assertRedirect("people/{$this->person->id}/history");
});

test('person deletion is logged', function () {
    $this->person->delete();

    expect($log = latestLog())
        ->log_name->toBe('people')
        ->description->toBe('deleted')
        ->subject->toBeModel($this->person);

    expect((string) $log->created_at)->toBe((string) $this->person->deleted_at);

    expect($log->properties->all())->toBe([
        'attributes' => ['deleted_at' => $this->person->deleted_at->toJson()],
    ]);
});
