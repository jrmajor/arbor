<?php

namespace App\Services\Pytlewski;

final class Pytlewski
{
    public function __construct(
        public readonly int $id,
        public readonly string $url,
        public readonly ?string $familyName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $name = null,
        public readonly ?string $middleName = null,
        public readonly ?string $birthDate = null,
        public readonly ?string $birthPlace = null,
        public readonly ?string $deathDate = null,
        public readonly ?string $deathPlace = null,
        public readonly ?string $burialPlace = null,
        public readonly ?string $photo = null,
        public readonly ?string $bio = null,
        public readonly ?Relative $mother = null,
        public readonly ?Relative $father = null,
        public readonly array $marriages = [],
        public readonly array $children = [],
        public readonly array $siblings = [],
    ) { }
}
