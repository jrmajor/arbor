<?php

namespace App\Services\Sources;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;
use Psl\Json;
use Psl\Type;
use Psl\Vec;

class SourcesCast implements CastsAttributes
{
    /**
     * @param ?string $value
     * @param array<string, mixed> $attributes
     * @return Collection<int, Source>
     */
    public function get($model, string $key, $value, array $attributes): Collection
    {
        $sources = Type\vec(Type\string())->coerce(Json\decode($value ?? '[]'));

        return collect($sources)->map(fn ($raw) => Source::from($raw));
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = Vec\map(
            Type\Vec(Type\union(
                Type\null(), Type\string(), Type\instance_of(Source::class),
            ))->coerce($value),
            fn ($source) => Source::from($source)->sanitized(),
        );

        return Json\encode(Vec\filter_nulls($value));
    }
}
