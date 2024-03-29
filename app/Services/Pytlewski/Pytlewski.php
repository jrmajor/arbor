<?php

namespace App\Services\Pytlewski;

final class Pytlewski
{
    public readonly string $url;

    public function __construct(
        public readonly int $id,
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
        /** @var list<Marriage> */
        public readonly array $marriages = [],
        /** @var list<Relative> */
        public readonly array $children = [],
        /** @var list<Relative> */
        public readonly array $siblings = [],
    ) {
        $this->url = PytlewskiFactory::url($id);
    }
}
