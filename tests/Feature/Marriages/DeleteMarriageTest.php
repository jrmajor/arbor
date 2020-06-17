<?php

use App\Marriage;
use Carbon\Carbon;

beforeEach(
    fn () => $this->marriage = factory(Marriage::class)->create()
);

test('guests cannot delete marriage', function () {
    delete('marriages/'.$this->marriage->id)
        ->assertStatus(302)
        ->assertRedirect('login');

    assertFalse($this->marriage->fresh()->trashed());
});

test('users without permissions cannot delete marriage', function () {
    withPermissions(1)
        ->delete('marriages/'.$this->marriage->id)
        ->assertStatus(403);

    assertFalse($this->marriage->fresh()->trashed());
});

test('users with permissions can delete marriage', function () {
    withPermissions(2)
        ->delete('marriages/'.$this->marriage->id)
        ->assertStatus(302)
        ->assertRedirect('people/'.$this->marriage->woman_id);

    assertTrue($this->marriage->fresh()->trashed());
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
