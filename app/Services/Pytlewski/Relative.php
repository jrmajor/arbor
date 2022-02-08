<?php

namespace App\Services\Pytlewski;

use App\Models\Person;

final class Relative
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $surname = null,
        public readonly ?Person $person = null,
    ) { }

    public function url(): ?string
    {
        return $this->id !== null ? Pytlewski::url((int) $this->id) : null;
    }
}
