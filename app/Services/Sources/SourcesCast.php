<?php

namespace App\Services\Sources;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\JsonEncodingException;

class SourcesCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return collect($model->fromJson($value))
            ->map(fn ($raw) => Source::from($raw));
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        $value = collect($value)
            ->map(fn ($source) => Source::from($source))
            ->map->sanitized()
            ->filter(fn ($source) => $source !== null)
            ->values()
            ->toJson();

        if ($value === false) {
            throw JsonEncodingException::forAttribute(
                $model, $key, json_last_error_msg(),
            );
        }

        return $value;
    }
}
