<?php

namespace App\Traits;

trait HasDateRanges
{
    public function initializeHasDateRanges()
    {
        foreach (static::$dateRanges as $dateRange) {
            $this->casts[$dateRange.'_from'] = 'datetime:Y-m-d';
            $this->casts[$dateRange.'_to'] = 'datetime:Y-m-d';
        }
    }
}
