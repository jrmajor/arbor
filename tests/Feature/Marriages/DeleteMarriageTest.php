<?php

use App\Models\Marriage;

use function Pest\Laravel\delete;
use function Tests\latestLog;
use function Tests\withPermissions;

beforeEach(function () {
    $this->marriage = Marriage::factory()->create();
});

test('guests cannot delete marriage', function () {
    delete("marriages/{$this->marriage->id}")
        ->assertStatus(302)
        ->assertRedirect('login');

    expect($this->marriage->fresh()->trashed())->toBeFalse();
});

test('users without permissions cannot delete marriage', function () {
    withPermissions(1)
        ->delete("marriages/{$this->marriage->id}")
        ->assertStatus(403);

    expect($this->marriage->fresh()->trashed())->toBeFalse();
});

test('users with permissions can delete marriage', function () {
    withPermissions(2)
        ->delete("marriages/{$this->marriage->id}")
        ->assertStatus(302);

    expect($this->marriage->fresh()->trashed())->toBeTrue();
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

    expect($log = latestLog())
        ->log_name->toBe('marriages')
        ->description->toBe('deleted')
        ->subject->toBeModel($this->marriage);

    expect((string) $log->created_at)->toBe((string) $this->marriage->deleted_at);

    expect($log->properties->all())->toBe([
        'attributes' => ['deleted_at' => $this->marriage->deleted_at->toJson()],
    ]);
});
