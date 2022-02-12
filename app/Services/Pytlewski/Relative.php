<?php

namespace App\Services\Pytlewski;

use App\Models\Person;

final class Relative
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $url = null,
        public readonly ?string $name = null,
        public readonly ?string $surname = null,
        public readonly ?Person $person = null,
    ) { }
}
