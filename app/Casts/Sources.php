<?php

namespace App\Casts;

use App\Source;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\JsonEncodingException;

class Sources implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return collect($model->fromJson($value))
                ->map(fn ($raw) => Source::from($raw));
    }

    public function set($model, $key, $value, $attributes)
    {
        if ($value === null) {
            return;
        }

        $value = collect($value)
                    ->map(fn ($source) => Source::from($source))
                    ->map->sanitized()
                    ->filter(fn ($source) => $source !== null)
                    ->values()
                    ->toJson();

        if ($value === false) {
            throw JsonEncodingException::forAttribute(
                $model, $key, json_last_error_msg()
            );
        }

        return $value;
    }
}
