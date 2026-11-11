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

    /**
     * The years of a date range's endpoints, or null when either is missing.
     *
     * @return ?array{int, int}
     */
    public function yearRange(string $dateRange): ?array
    {
        $from = $this->{"{$dateRange}_from"};
        $to = $this->{"{$dateRange}_to"};

        return $from && $to ? [$from->year, $to->year] : null;
    }
}
