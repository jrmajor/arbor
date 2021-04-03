<?php

use App\Models\Person;
use function Pest\Laravel\get;
use function Tests\withPermissions;

test('guest cannot see hidden alive person', function () {
    $person = Person::factory()->alive()->create();

    get("people/{$person->id}")
        ->assertStatus(403);
});

test('guest cannot see hidden dead person', function () {
    $person = Person::factory()->dead()->create();

    get("people/{$person->id}")
        ->assertStatus(403);
});

test('guest can see visible alive person', function () {
    $person = Person::factory()->alive()->create([
        'visibility' => 1,
    ]);

    get("people/{$person->id}")
        ->assertStatus(200);
});

test('guest can see visible dead person', function () {
    $person = Person::factory()->dead()->create([
        'visibility' => 1,
    ]);

    get("people/{$person->id}")
        ->assertStatus(200);
});

test('user with persmissions can see hidden alive person', function () {
    $person = Person::factory()->alive()->create();

    withPermissions(1)
        ->get("people/{$person->id}")
        ->assertStatus(200);
});

test('user with persmissions can see hidden dead person', function () {
    $person = Person::factory()->dead()->create();

    withPermissions(1)
        ->get("people/{$person->id}")
        ->assertStatus(200);
});

test('user with persmissions can see visible alive person', function () {
    $person = Person::factory()->alive()->create([
        'visibility' => 1,
    ]);

    withPermissions(1)
        ->get("people/{$person->id}")
        ->assertStatus(200);
});

test('user with persmissions can see visible dead person', function () {
    $person = Person::factory()->dead()->create([
        'visibility' => 1,
    ]);

    withPermissions(1)
        ->get("people/{$person->id}")
        ->assertStatus(200);
});

test('guest see 404 when attemting to view nonexistent person')
    ->get('people/1')
    ->assertStatus(404);

test('user with insufficient persmissions see 404 when attemting to view nonexistent person')
    ->withPermissions(1)
    ->get('people/1')
    ->assertStatus(404);

test('guest see 404 when attemting to view deleted person', function () {
    $person = tap(Person::factory()->create())->delete();

    get("people/{$person->id}")
        ->assertStatus(404);
});

test('users without persmissions see 404 when attemting to view deleted person', function () {
    $person = tap(Person::factory()->create())->delete();

    withPermissions(2)
        ->get("people/{$person->id}")
        ->assertStatus(404);
});

test('user with persmissions are redirected to edits history when attempting to view deleted person', function () {
    $person = tap(Person::factory()->create())->delete();

    withPermissions(3)
        ->get("people/{$person->id}")
        ->assertStatus(302)
        ->assertRedirect("people/{$person->id}/history");
});
