<?php

namespace App\Services;

use Illuminate\Support\Str;
use Spatie\Flash\Flash;
use Spatie\Flash\Message as FlashMessage;

function formatBiography(?string $biography): ?string
{
    if ($biography === null) {
        return null;
    }

    return (string) Str::of($biography)
        ->trim()
        ->replace(["\r\n", "\r"], "\n")
        ->pipe(e(...))
        ->prepend('<p>')
        ->append('</p>')
        ->replace("\n\n", "</p>\n<p>");
}

function flash(string $class, string $text): void
{
    app(Flash::class)->flash(new FlashMessage(__($text), $class));
}
