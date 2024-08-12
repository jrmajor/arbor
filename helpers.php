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

function faker(): Generator
{
    return app(Generator::class);
}
