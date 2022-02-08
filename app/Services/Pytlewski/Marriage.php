<?php

namespace App\Services\Pytlewski;

/**
 * @property-read ?string $date
 * @property-read ?string $place
 */
class Marriage extends Relative
{
    protected array $keys = ['id', 'name', 'surname', 'date', 'place', 'person'];
}
