<?php

namespace Tests\Unit\Pytlewski;

use Generator;
use Psl\File;

trait UsesPytlewskiDataset
{
    /**
     * @return Generator<string, array{int, string, array<string, mixed>}>
     */
    private static function provideScrapeCases(): Generator
    {
        foreach ([
            543 => 'Franciszek Kosela',
            556 => 'Józef Major',
            550 => 'Iwona Bobińska',
            704 => 'Maciej Kazimierz Pytlewski',
            1420 => 'Maryanna Pytlewska',
            1928 => 'Maria Ludwika Chełmońska',
            4613 => 'Adolfina Białkowska',
        ] as $id => $name) {
            yield "{$id} ({$name})" => [
                $id,
                File\read(__DIR__ . "/../../Datasets/Pytlewscy/{$id}.html"),
                require __DIR__ . "/../../Datasets/Pytlewscy/{$id}.php",
            ];
        }
    }
}
