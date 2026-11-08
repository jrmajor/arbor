<?php

namespace App\Models\Traits;

use App\Models\Casts\DateRangeToDateCast;
use App\Models\Casts\DateRangeToYearCast;
use Illuminate\Database\Eloquent\Attributes\Initialize;
use InvalidArgumentException;
use Psl\Str;

trait HasDateRanges
{
    #[Initialize]
    public function initializeDateRanges(): void
    {
        foreach (static::$dateRanges as $dateRange) {
            if (! Str\ends_with($dateRange, '_date')) {
                throw new InvalidArgumentException();
            }

            $this->casts["{$dateRange}_from"] = 'datetime:Y-m-d';
            $this->casts["{$dateRange}_to"] = 'datetime:Y-m-d';

            $this->casts[Str\replace($dateRange, 'date', 'year')] = DateRangeToYearCast::class;
            $this->casts[$dateRange] = DateRangeToDateCast::class;
        }
    }
}
