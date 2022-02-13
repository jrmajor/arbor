<?php

namespace App;

use Psl\Str;
use Psl\Type;

/**
 * Returns trimmed string or null if it's empty.
 */
function nullable_trim(?string $string): ?string
{
    $string = Str\trim($string ?? '');

    return $string === '' ? null : $string;
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
