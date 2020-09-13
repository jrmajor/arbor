<?php

use App\Models\Person;
use Carbon\Carbon;
use function Pest\Laravel\delete;

beforeEach(
    fn () => $this->person = Person::factory()->create()
);

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

    $log = latestLog();

    expect($log->log_name)->toBe('people');
    expect($log->description)->toBe('deleted');
    expect($this->person->is($log->subject))->toBeTrue();

    expect((string) $log->created_at)->toBe((string) $this->person->deleted_at);

    expect($log->properties['attributes']['deleted_at'])
        ->toBe($this->person->deleted_at->toJson());

    expect($log->properties)->toHaveCount(1);
    expect($log->properties['attributes'])->toHaveCount(1);
});
