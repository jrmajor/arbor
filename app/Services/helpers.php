<?php

namespace App\Services;

use Psl\Fun;
use Psl\Str;
use Spatie\Flash\Flash;
use Spatie\Flash\Message as FlashMessage;

function formatBiography(?string $biography): ?string
{
    return $biography === null ? null : Fun\pipe(
        fn ($s) => Str\trim($s),
        fn ($s) => Str\replace_every($s, ["\r\n" => "\n", "\r" => "\n"]),
        fn ($s) => e($s),
        fn ($s) => "<p>{$s}</p>",
        fn ($s) => Str\replace($s, "\n\n", "</p>\n<p>"),
    )($biography);
}

function flash(string $class, string $text): void
{
    app(Flash::class)->flash(new FlashMessage(__($text), $class));
}
