<?php

use App\Models\Person;

it('works with no people')
    ->get('/people/f/H')
    ->assertStatus(404);

it('works with people', function () {
    withPermissions(1);

    Person::factory()->create([
        'family_name' => 'Zbyrowski',
        'last_name' => null,
    ]);

    Person::factory()->create([
        'family_name' => 'Ziobro',
        'last_name' => 'Mikke',
    ]);

    get('/people/f/Z')
        ->assertStatus(200)
        ->assertSeeText('Ziobro')
        ->assertSeeText('Mikke');

    get('/people/l/Z')
        ->assertStatus(200)
        ->assertSeeText('Zbyrowski')
        ->assertDontSeeText('Mikke');

    get('/people/l/M')
        ->assertStatus(200)
        ->assertDontSeeText('Zbyrowski')
        ->assertSeeText('Mikke');
});

it('hides sensitive data to guests', function () {
    Person::factory()->alive()->create([
        'family_name' => 'Ziobro',
        'last_name' => 'Mikke',
    ]);

    get('/people/f/Z')
        ->assertStatus(200)
        ->assertSeeText('[hidden]')
        ->assertDontSeeText('Ziobro')
        ->assertDontSeeText('Mikke');
});
