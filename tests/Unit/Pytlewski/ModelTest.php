<?php

use App\Services\Pytlewski\Pytlewski;
use Illuminate\Support\Arr;

it('can make proper url')
    ->assertEquals(
        'http://www.pytlewski.pl/index/drzewo/index.php?view=true&id=556',
        Pytlewski::url(556)
    );

it('requests source from pytlewski.pl', function () {
    Http::fake();

    new Pytlewski(556);

    Http::assertSent(
        fn ($request) => $request->url() == 'http://www.pytlewski.pl/index/drzewo/index.php?view=true&id=556'
    );
});

it('caches parsed attributes from pytlewski.pl', function () {
    Http::fake();

    Cache::shouldReceive('remember')
        ->once()
        ->with(
            'pytlewski.556',
            Carbon\CarbonInterval::class,
            \Closure::class
        );

    new Pytlewski(556);

    Http::assertSentCount(0);
});

it('properly scrapes pytlewski.pl', function ($id, $source, $attributes) {
    Http::fake([
        Pytlewski::url($id) => Http::response($source, 200),
    ]);

    $pytlewski = new Pytlewski($id);

    $keysToCheck = [
        'family_name', 'last_name', 'name', 'middle_name',
        'birth_date', 'birth_place', 'death_date', 'death_place',
        'photo', 'bio'
    ];

    foreach (Arr::only($attributes, $keysToCheck) as $key => $value) {
        expect($pytlewski->$key)->toBe($value);
        // "Pytlewski $id on key $key."
    }
})->with('pytlewscy');
