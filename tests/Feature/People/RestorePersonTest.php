<?php

use App\Person;
use Carbon\Carbon;

beforeEach(
    fn () => $this->person = tap(factory(Person::class)->create())->delete()
);

test('guests cannot restore person', function () {
    patch("people/{$this->person->id}/restore")
        ->assertStatus(302)
        ->assertRedirect('login');

    assertTrue($this->person->fresh()->trashed());
});

test('users without permissions cannot restore person', function () {
    withPermissions(2)
        ->patch("people/{$this->person->id}/restore")
        ->assertStatus(403);

    assertTrue($this->person->fresh()->trashed());
});

test('users with permissions can restore person', function () {
    withPermissions(3)
        ->patch("people/{$this->person->id}/restore")
        ->assertStatus(302)
        ->assertRedirect("people/{$this->person->id}");

    assertFalse($this->person->fresh()->trashed());
});

test('person can be restored only when deleted', function () {
    $this->person->restore();

    withPermissions(3)
        ->patch("people/{$this->person->id}/restore")
        ->assertStatus(404);
});

test('person restoration is logged', function () {
    $this->person->restore();

    $log = latestLog();

    assertEquals('people', $log->log_name);
    assertEquals('restored', $log->description);
    assertTrue($this->person->is($log->subject));

    assertEquals(null, $log->properties['attributes']['deleted_at']);

    assertCount(1, $log->properties);
    assertCount(1, $log->properties['attributes']);
});
