<?php

use App\Marriage;

beforeEach(
    fn () => $this->marriage = tap(factory(Marriage::class)->create())->delete()
);

test('guests cannot restore marriage', function () {
    patch("marriages/{$this->marriage->id}/restore")
        ->assertStatus(302)
        ->assertRedirect('login');

    assertTrue($this->marriage->fresh()->trashed());
});

test('users without permissions cannot restore marriage', function () {
    withPermissions(2)
        ->patch("marriages/{$this->marriage->id}/restore")
        ->assertStatus(403);

    assertTrue($this->marriage->fresh()->trashed());
});

test('users with permissions can restore marriage', function () {
    withPermissions(3)
        ->patch("marriages/{$this->marriage->id}/restore")
        ->assertStatus(302)
        ->assertRedirect("people/{$this->marriage->woman_id}");

    assertFalse($this->marriage->fresh()->trashed());
});

test('marriage can be restored only when deleted', function () {
    $this->marriage->restore();

    withPermissions(3)
        ->patch("marriages/{$this->marriage->id}/restore")
        ->assertStatus(404);
});

test('marriage restoration is logged', function () {
    $this->marriage->restore();

    $log = latestLog();

    assertEquals('marriages', $log->log_name);
    assertEquals('restored', $log->description);
    assertTrue($this->marriage->is($log->subject));

    assertEquals(null, $log->properties['attributes']['deleted_at']);

    assertCount(1, $log->properties);
    assertCount(1, $log->properties['attributes']);
});
