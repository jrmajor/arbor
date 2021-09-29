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
        'visibility' => true,
    ]);

    get("people/{$person->id}")
        ->assertStatus(200);
});

test('guest can see visible dead person', function () {
    $person = Person::factory()->dead()->create([
        'visibility' => true,
    ]);

    get("people/{$person->id}")
        ->assertStatus(200);
});

test('user with permissions can see hidden alive person', function () {
    $person = Person::factory()->alive()->create();

    withPermissions(1)
        ->get("people/{$person->id}")
        ->assertStatus(200);
});

test('user with permissions can see hidden dead person', function () {
    $person = Person::factory()->dead()->create();

    withPermissions(1)
        ->get("people/{$person->id}")
        ->assertStatus(200);
});

test('user with permissions can see visible alive person', function () {
    $person = Person::factory()->alive()->create([
        'visibility' => true,
    ]);

    withPermissions(1)
        ->get("people/{$person->id}")
        ->assertStatus(200);
});

test('user with permissions can see visible dead person', function () {
    $person = Person::factory()->dead()->create([
        'visibility' => true,
    ]);

    withPermissions(1)
        ->get("people/{$person->id}")
        ->assertStatus(200);
});

test('guest see 404 when attempting to view nonexistent person')
    ->get('people/1')
    ->assertStatus(404);

test('user with insufficient permissions see 404 when attempting to view nonexistent person')
    ->withPermissions(1)
    ->get('people/1')
    ->assertStatus(404);

test('guest see 404 when attempting to view deleted person', function () {
    $person = Person::factory()->create(['deleted_at' => now()]);

    get("people/{$person->id}")
        ->assertStatus(404);
});

test('users without permissions see 404 when attempting to view deleted person', function () {
    $person = Person::factory()->create(['deleted_at' => now()]);

    withPermissions(2)
        ->get("people/{$person->id}")
        ->assertStatus(404);
});

test('user with permissions are redirected to edits history when attempting to view deleted person', function () {
    $person = Person::factory()->create(['deleted_at' => now()]);

    withPermissions(3)
        ->get("people/{$person->id}")
        ->assertStatus(302)
        ->assertRedirect("people/{$person->id}/history");
});
