<?php

use App\Services\Pytlewski\Pytlewski;
use Carbon\CarbonInterval;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

it('can make proper url')
    ->expect(Pytlewski::url(556))
    ->toBe('http://www.pytlewski.pl/index/drzewo/index.php?view=true&id=556');

it('requests source from pytlewski.pl', function () {
    Http::fake();

    new Pytlewski(556);

    Http::assertSent(
        fn ($request) => $request->url() === 'http://www.pytlewski.pl/index/drzewo/index.php?view=true&id=556',
    );
});

it('caches parsed attributes from pytlewski.pl', function () {
    Http::fake();

    Cache::shouldReceive('remember')
        ->once()
        ->with('pytlewski.556', CarbonInterval::class, Closure::class)
        ->andReturn([]);

    new Pytlewski(556);

    Http::assertSentCount(0);
});

it('properly scrapes pytlewski.pl', function ($id, $source, $attributes) {
    Http::fake([
        Pytlewski::url($id) => Http::response($source),
    ]);

    $pytlewski = new Pytlewski($id);

    $keysToCheck = [
        'family_name', 'last_name', 'name', 'middle_name',
        'birth_date', 'birth_place', 'death_date', 'death_place',
        'photo', 'bio',
    ];

    foreach (Arr::only($attributes, $keysToCheck) as $key => $value) {
        expect($pytlewski->{$key})->toBe($value);
    }
})->with('pytlewscy');

it('throws an exception when a key does not exist', function () {
    Cache::shouldReceive('remember')
        ->andReturn([]);

    $pytlewski = new Pytlewski(556);

    $pytlewski->nonexistentKey;
})->throws(InvalidArgumentException::class, 'Key [nonexistentKey] does not exist.');
