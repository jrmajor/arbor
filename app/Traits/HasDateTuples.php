<?php

namespace App\Traits;

trait HasDateTuples
{
    public function initializeHasDateTuples()
    {
        foreach (static::$dateTuples as $dateTuple) {
            $this->casts[$dateTuple.'_from'] = 'datetime:Y-m-d';
            $this->casts[$dateTuple.'_to'] = 'datetime:Y-m-d';
        }
    }
}
