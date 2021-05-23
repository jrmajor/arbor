<?php

use App\Models\Person;
use Illuminate\Support\Collection;

it('casts sources to collection', function () {
    $sources = Person::factory()->create([
        'sources' => null,
    ])->sources;

    expect($sources)->toBeInstanceOf(Collection::class)->toBeEmpty();

    $sources = Person::factory()->create([
        'sources' => [],
    ])->sources;

    expect($sources)->toBeInstanceOf(Collection::class)->toBeEmpty();

    $sources = Person::factory()->create([
        'sources' => [
            '[Henryk Gąsiorowski](https://pl.wikipedia.org/wiki/Henryk_G%C4%85siorowski) w Wikipedii, wolnej encyklopedii, dostęp 2020-06-06',
            'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
        ],
    ])->sources;

    expect($sources)->toBeInstanceOf(Collection::class)->toHaveCount(2);
});

test('sources are sanitized', function () {
    $raw = [
        'a' => "     [Henryk    Gąsiorowski](https://pl.wikipedia.org/wiki/Henryk_G%C4%85siorowski)  \t   w Wikipedii,\nwolnej encyklopedii, dostęp 2020-06-06\r\n",
        'b' => "    \n",
        null,
        'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
    ];

    $sanitized = [
        '[Henryk Gąsiorowski](https://pl.wikipedia.org/wiki/Henryk_G%C4%85siorowski) w Wikipedii, wolnej encyklopedii, dostęp 2020-06-06',
        'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
    ];

    expect(
        Person::factory()
            ->create(['sources' => $raw])
            ->sources->map->raw()->all(),
    )->toBe($sanitized);
});
