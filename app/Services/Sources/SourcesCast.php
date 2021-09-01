<?php

namespace App\Services\Sources;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Support\Collection;

class SourcesCast implements CastsAttributes
{
    /**
     * @return Collection<int, Source>
     */
    public function get($model, string $key, $value, array $attributes): Collection
    {
        return collect($model->fromJson($value))
            ->map(fn ($raw) => Source::from($raw));
    }

    public function set($model, string $key, $value, array $attributes): ?string
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

        /** @phpstan-ignore-next-line */
        if ($value === false) {
            throw JsonEncodingException::forAttribute(
                $model, $key, json_last_error_msg(),
            );
        }

        return $value;
    }
}
