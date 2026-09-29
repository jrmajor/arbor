<?php

namespace Tests\Unit\Wielcy;

use Generator;
use Psl\File;

trait UsesWielcyDataset
{
    /**
     * @return Generator<string, array{string, string, array<string, mixed>}>
     */
    public static function provideScrapeCases(): Generator
    {
        foreach ([
            'psb.6305.1' => 'Henryk Gąsiorowski',
            'psb.6305.3' => 'Maria Stecher de Sebenitz',
            'sw.476934' => 'Filaret Sembratowicz',
            'dw.3' => 'Mieszko I',
            'cz.I017795' => 'Bronisław Komorowski',
            'psb.30118.12' => 'Maria Skłodowska',
        ] as $id => $name) {
            yield "{$id} ({$name})" => [
                $id,
                File\read(__DIR__ . "/../../Datasets/Wielcy/{$id}.html"),
                require __DIR__ . "/../../Datasets/Wielcy/{$id}.php",
            ];
        }
    }
}
