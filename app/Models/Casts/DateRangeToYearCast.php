<?php

namespace App\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DateRangeToYearCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?int
    {
        $from = str_replace('year', 'date_from', $key);
        $to = str_replace('year', 'date_to', $key);

        if ($model->{$from}?->year !== $model->{$to}?->year) {
            return null;
        }

        return $model->{$from}?->year;
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
    }
}
