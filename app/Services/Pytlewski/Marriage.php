<?php

namespace App\Services\Pytlewski;

use App\Models\Person;

final class Marriage
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $url = null,
        public readonly ?string $name = null,
        public readonly ?string $surname = null,
        public readonly ?string $date = null,
        public readonly ?string $place = null,
        public readonly ?Person $person = null,
    ) { }
}
