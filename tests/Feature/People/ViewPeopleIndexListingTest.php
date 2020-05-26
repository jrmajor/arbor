<?php

use App\Person;

it('works with no people')
    ->get('/people')
    ->assertStatus(200)
    ->assertSeeText('total: 0');

it('works with people', function () {
    factory(Person::class)->create([
        'family_name' => 'Zbyrowski',
        'last_name' => null,
    ]);

    factory(Person::class)->create([
        'family_name' => 'Ziobro',
        'last_name' => 'Mikke',
    ]);

    get('/people')
        ->assertStatus(200)
        ->assertSeeText('Z [2]')
        ->assertSeeText('M [1]')
        ->assertSeeText('Z [1]');
});
