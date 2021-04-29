<?php

namespace App\Models\Traits;

use App\Models\Casts\DateRangeToDateCast;
use App\Models\Casts\DateRangeToYearCast;
use InvalidArgumentException;

trait HasDateRanges
{
    public function initializeHasDateRanges()
    {
        foreach (static::$dateRanges as $dateRange) {
            if (! str_ends_with($dateRange, '_date')) {
                throw new InvalidArgumentException();
            }

            $this->casts["{$dateRange}_from"] = 'datetime:Y-m-d';
            $this->casts["{$dateRange}_to"] = 'datetime:Y-m-d';

            $this->casts[str_replace('date', 'year', $dateRange)] = DateRangeToYearCast::class;
            $this->casts[$dateRange] = DateRangeToDateCast::class;
        }
    }
}
