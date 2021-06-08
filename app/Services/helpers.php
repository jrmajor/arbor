<?php

namespace App\Services;

use Illuminate\Support\Str;

function formatBiography(?string $biography): ?string
{
    if ($biography === null) {
        return null;
    }

    return (string) Str::of($biography)
        ->trim()
        ->replace(["\r\n", "\r"], "\n")
        ->pipe('e')
        ->prepend('<p>')
        ->append('</p>')
        ->replace("\n\n", "</p>\n<p>");
}
