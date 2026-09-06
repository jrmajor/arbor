<?php

namespace App;

use Inertia\Inertia;
use Psl\Dict;
use Psl\Str;
use Psl\Type;

function flash(string $level, string $text): void
{
    Inertia::flash('notification', [
        'level' => $level,
        'message' => __($text),
    ]);
}

/**
 * Returns trimmed string or null if it's empty.
 */
function nullable_trim(?string $string): ?string
{
    $string = Str\trim($string ?? '');

    return $string === '' ? null : $string;
}

/**
 * Applies nullable trim to each value.
 *
 * @template T
 *
 * @param array<T, ?string> $array
 *
 * @return array<T, ?string>
 */
function trim_values(array $array): array
{
    return Dict\map($array, fn (?string $v) => nullable_trim($v));
}

/**
 * Trims string and coerces it to integer.
 * Returns null if string is non-numeric.
 */
function parse_int(?string $string): ?int
{
    if (null === $string = nullable_trim($string)) {
        return null;
    }

    try {
        return Type\int()->coerce($string);
    } catch (Type\Exception\CoercionException) {
        return null;
    }
}
