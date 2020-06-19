<?php

use App\Marriage;
use Carbon\Carbon;

beforeEach(
    fn () => $this->marriage = factory(Marriage::class)->create()
);

test('guests cannot delete marriage', function () {
    delete("marriages/{$this->marriage->id}")
        ->assertStatus(302)
        ->assertRedirect('login');

    assertFalse($this->marriage->fresh()->trashed());
});

test('users without permissions cannot delete marriage', function () {
    withPermissions(1)
        ->delete("marriages/{$this->marriage->id}")
        ->assertStatus(403);

    assertFalse($this->marriage->fresh()->trashed());
});

test('users with permissions can delete marriage', function () {
    withPermissions(2)
        ->delete("marriages/{$this->marriage->id}")
        ->assertStatus(302);

    assertTrue($this->marriage->fresh()->trashed());
});

test('users without permissions to view history are redirected to woman page', function () {
    withPermissions(2)
        ->delete("marriages/{$this->marriage->id}")
        ->assertStatus(302)
        ->assertRedirect("people/{$this->marriage->woman_id}");
});

test('users with permissions to view history are redirected to marriage history', function () {
    withPermissions(3)
        ->delete("marriages/{$this->marriage->id}")
        ->assertStatus(302)
        ->assertRedirect("marriages/{$this->marriage->id}/history");
});

test('marriage deletion is logged', function () {
    $this->marriage->delete();

    $log = latestLog();

    assertEquals('marriages', $log->log_name);
    assertEquals('deleted', $log->description);
    assertTrue($this->marriage->is($log->subject));

    assertEquals($this->marriage->deleted_at, $log->created_at);

    assertEquals(
        $this->marriage->deleted_at,
        Carbon::create($log->properties['attributes']['deleted_at'])
    );

    assertCount(1, $log->properties);
    assertCount(1, $log->properties['attributes']);
});
