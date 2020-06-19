<?php

use App\Person;
use Carbon\Carbon;

beforeEach(
    fn () => $this->person = factory(Person::class)->create()
);

test('guests cannot delete person', function () {
    delete("people/{$this->person->id}")
        ->assertStatus(302)
        ->assertRedirect('login');

    assertFalse($this->person->fresh()->trashed());
});

test('users without permissions cannot delete person', function () {
    withPermissions(1)
        ->delete("people/{$this->person->id}")
        ->assertStatus(403);

    assertFalse($this->person->fresh()->trashed());
});

test('users with permissions can delete person', function () {
    withPermissions(2)
        ->delete("people/{$this->person->id}")
        ->assertStatus(302);

    assertTrue($this->person->fresh()->trashed());
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

    assertEquals('people', $log->log_name);
    assertEquals('deleted', $log->description);
    assertTrue($this->person->is($log->subject));

    assertEquals($this->person->deleted_at, $log->created_at);

    assertEquals(
        $this->person->deleted_at,
        Carbon::create($log->properties['attributes']['deleted_at'])
    );

    assertCount(1, $log->properties);
    assertCount(1, $log->properties['attributes']);
});
