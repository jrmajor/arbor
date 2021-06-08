<?php

use App\Models\Marriage;

use function Pest\Laravel\patch;
use function Tests\latestLog;
use function Tests\withPermissions;

beforeEach(function () {
    $this->marriage = tap(Marriage::factory()->create())->delete();
});

test('guests cannot restore marriage', function () {
    patch("marriages/{$this->marriage->id}/restore")
        ->assertStatus(302)
        ->assertRedirect('login');

    expect($this->marriage->fresh()->trashed())->toBeTrue();
});

test('users without permissions cannot restore marriage', function () {
    withPermissions(2)
        ->patch("marriages/{$this->marriage->id}/restore")
        ->assertStatus(403);

    expect($this->marriage->fresh()->trashed())->toBeTrue();
});

test('users with permissions can restore marriage', function () {
    withPermissions(3)
        ->patch("marriages/{$this->marriage->id}/restore")
        ->assertStatus(302)
        ->assertRedirect("people/{$this->marriage->woman_id}");

    expect($this->marriage->fresh()->trashed())->toBeFalse();
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

    expect($log->log_name)->toBe('marriages');
    expect($log->description)->toBe('restored');
    expect($log->subject())->toBeModel($this->marriage);

    expect($log->properties['attributes']['deleted_at'])->toBeNull();

    expect($log->properties)->toHaveCount(1);
    expect($log->properties['attributes'])->toHaveCount(1);
});
