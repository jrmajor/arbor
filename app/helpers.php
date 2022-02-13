<?php

namespace App;

use Illuminate\Support\HtmlString;
use Psl\Fun;
use Psl\Str;
use Psl\Type;
use Spatie\Flash\Flash;
use Spatie\Flash\Message as FlashMessage;

function flash(string $class, string $text): void
{
    app(Flash::class)->flash(new FlashMessage(__($text), $class));
}

function formatBiography(?string $biography): HtmlString
{
    if ($biography === null) {
        return new HtmlString();
    }

    /** @phpstan-ignore-next-line */
    return Fun\pipe(
        fn ($s) => Str\trim($s),
        fn ($s) => Str\replace_every($s, ["\r\n" => "\n", "\r" => "\n"]),
        fn ($s) => e($s),
        fn ($s) => "<p>{$s}</p>",
        fn ($s) => Str\replace($s, "\n\n", "</p>\n<p>"),
        fn ($s) => new HtmlString($s),
    )($biography);
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
