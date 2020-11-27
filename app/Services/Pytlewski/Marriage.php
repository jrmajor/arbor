<?php

namespace App\Services\Pytlewski;

class Marriage extends Relative
{
    protected array $keys = [
        'id', 'person',
        'name', 'surname',
        'date', 'place',
    ];
}
