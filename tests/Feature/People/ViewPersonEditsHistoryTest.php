<?php

use App\Person;

beforeEach(function() {
    $this->person = factory(Person::class)->create();
});

test('guests are asked to log in when attempting to view person history', function () {
    get("people/{$this->person->id}/history")
        ->assertStatus(302)
        ->assertRedirect('login');
});

test('users without permissions cannot view person history', function () {
    withPermissions(2)
        ->get("people/{$this->person->id}/history")
        ->assertStatus(403);
});

test('users with permissions can view person history', function () {
    withPermissions(3)
        ->get("people/{$this->person->id}/history")
        ->assertStatus(200);
});
