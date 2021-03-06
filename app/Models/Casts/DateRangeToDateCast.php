<?php

namespace App\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DateRangeToDateCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        $from = "{$key}_from";
        $to = "{$key}_to";

        if (! $model->{$from} || ! $model->{$to}) {
            return null;
        }

        return $model->{$from}->formatPeriodTo($model->{$to});
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
    }
}
