<?php

namespace App\Models;

final readonly class Letter
{
    public function __construct(
        public string $letter,
        public int $count,
    ) { }
}
