<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;
use Psl\Str;

final class NormalizeLinebreaks extends TransformsRequest
{
    protected function transform($key, $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        return Str\replace_every($value, ["\r\n" => "\n", "\r" => "\n"]);
    }
}
