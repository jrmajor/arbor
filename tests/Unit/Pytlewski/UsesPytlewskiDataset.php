<?php

namespace Tests\Unit\Pytlewski;

use Generator;

trait UsesPytlewskiDataset
{
    private function provideScrapeCases(): Generator
    {
        foreach ([
            543 => 'Franciszek Kosela',
            556 => 'Józef Major',
            550 => 'Iwona Bobińska (Kosela)',
            704 => 'Maciej Kazimierz Pytlewski',
            1420 => 'Maryanna Pytlewska (Ptakowska)',
            1928 => 'Maria Ludwika Chełmońska (Korwin-Szymanowska)',
            4613 => 'Adolfina Białkowska (Kolin)',
        ] as $id => $name) {
            yield $name => [
                $id,
                file_get_contents(__DIR__ . "/../../Datasets/Pytlewscy/{$id}.html"),
                require __DIR__ . "/../../Datasets/Pytlewscy/{$id}.php",
            ];
        }
    }
}
