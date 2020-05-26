<?php

use App\Person;

test('guest cannot see hidden alive person', function () {
    $person = factory(Person::class)->states('alive')->create();

    get("people/$person->id")
        ->assertStatus(403);
});

test('guest cannot see hidden dead person', function () {
    $person = factory(Person::class)->states('dead')->create();

    get("people/$person->id")
        ->assertStatus(403);
});

test('guest can see visible alive person', function () {
    $person = factory(Person::class)->states('alive')->create([
        'visibility' => 1,
    ]);

    get("people/$person->id")
        ->assertStatus(200);
});

test('guest can see visible dead person', function () {
    $person = factory(Person::class)->states('dead')->create([
        'visibility' => 1,
    ]);

    get("people/$person->id")
        ->assertStatus(200);
});

test('user with persmissions can see hidden alive person', function () {
    $person = factory(Person::class)->states('alive')->create();

    withPermissions(1)
        ->get("people/$person->id")
        ->assertStatus(200);
});

test('user with persmissions can see hidden dead person', function () {
    $person = factory(Person::class)->states('dead')->create();

    withPermissions(1)
        ->get("people/$person->id")
        ->assertStatus(200);
});

test('user with persmissions can see visible alive person', function () {
    $person = factory(Person::class)->states('alive')->create([
        'visibility' => 1,
    ]);

    withPermissions(1)
        ->get("people/$person->id")
        ->assertStatus(200);
});

test('user with persmissions can see visible dead person', function () {
    $person = factory(Person::class)->states('dead')->create([
        'visibility' => 1,
    ]);

    withPermissions(1)
        ->get("people/$person->id")
        ->assertStatus(200);
});

test('guest see 404 when attemting to view nonexistent person')
    ->get('people/1')
    ->assertStatus(404);

test('user with persmissions see 404 when attemting to view nonexistent person')
    ->withPermissions(1)
    ->get('people/1')
    ->assertStatus(404);
