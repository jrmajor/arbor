<?php

use App\Services\Pytlewski\Pytlewski;

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

    foreach ($attributes as $key => $value) {
        assertEquals(
            $value, $pytlewski->$key,
            "Pytlewski $id on key $key."
        );
    }
})->with('pytlewscy');
