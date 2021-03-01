<?php

namespace App\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DateRangeToYearCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        $from = str_replace('year', 'date_from', $key);
        $to = str_replace('year', 'date_to', $key);

        return $model->{$from}?->year === $model->{$to}?->year
            ? $model->{$from}?->year : null;
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
    }
}
