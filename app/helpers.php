<?php

namespace App;

use Psl\Dict;
use Psl\Str;
use Psl\Type;
use Spatie\Flash\Flash;
use Spatie\Flash\Message as FlashMessage;

function flash(string $class, string $text): void
{
    app(Flash::class)->flash(new FlashMessage(__($text), $class));
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
