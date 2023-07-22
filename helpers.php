<?php

declare(strict_types=1);

use Carbon\Carbon;
use Faker\Generator;

function carbon(
    int|string|null $year = 0,
    ?int $month = 1,
    ?int $day = 1,
    ?int $hour = 0,
    ?int $minute = 0,
    ?int $second = 0,
    DateTimeZone|string|null $tz = null,
): ?Carbon {
    return Carbon::create(...func_get_args()) ?: null;
}

function roman(int $number): string
{
    $map = [
        'M' => 1000,
        'CM' => 900,
        'D' => 500,
        'CD' => 400,
        'C' => 100,
        'XC' => 90,
        'L' => 50,
        'XL' => 40,
        'X' => 10,
        'IX' => 9,
        'V' => 5,
        'IV' => 4,
        'I' => 1,
    ];

    $returnValue = '';

    while ($number > 0) {
        foreach ($map as $roman => $int) {
            if ($number >= $int) {
                $number -= $int;
                $returnValue .= $roman;

                break;
            }
        }
    }

    return $returnValue;
}

function faker(): Generator
{
    return app(Generator::class);
}
