<?php

namespace App\Services\Pytlewski;

class Marriage extends Relative
{
    protected $keys = [
        'id', 'person',
        'name', 'surname',
        'date', 'place',
    ];
}
