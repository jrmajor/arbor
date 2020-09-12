<?php

use App\Models\Marriage;

beforeEach(function () {
    $this->marriage = Marriage::factory()->create();
});

test('guests are asked to log in when attempting to view marriage history', function () {
    get("marriages/{$this->marriage->id}/history")
        ->assertStatus(302)
        ->assertRedirect('login');
});

test('users without permissions cannot view marriage history', function () {
    withPermissions(2)
        ->get("marriages/{$this->marriage->id}/history")
        ->assertStatus(403);
});

test('users with permissions can view marriage history', function () {
    withPermissions(3)
        ->get("marriages/{$this->marriage->id}/history")
        ->assertStatus(200);
});
