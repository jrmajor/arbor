<?php

namespace App\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * @implements CastsAttributes<string, never>
 */
class DateRangeToDateCast implements CastsAttributes
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function get($model, string $key, $value, array $attributes): ?string
    {
        $from = "{$key}_from";
        $to = "{$key}_to";

        if (! $model->{$from} || ! $model->{$to}) {
            return null;
        }

        return $model->{$from}->formatPeriodTo($model->{$to});
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
    }
}
