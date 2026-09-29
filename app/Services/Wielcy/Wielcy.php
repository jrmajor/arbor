<?php

namespace App\Services\Wielcy;

final class Wielcy
{
    public readonly string $url;

    public function __construct(
        public readonly string $id,
        public readonly ?string $name = null,
        public readonly ?string $middleName = null,
        public readonly ?string $surname = null,
        /** @var 'xy'|'xx'|null */
        public readonly ?string $sex = null,
        public readonly ?string $birthDate = null,
        public readonly ?string $birthPlace = null,
        public readonly ?string $deathDate = null,
        public readonly ?string $deathPlace = null,
        public readonly ?string $burialPlace = null,
        public readonly ?string $photo = null,
        public readonly ?Relative $mother = null,
        public readonly ?Relative $father = null,
    ) {
        $this->url = WielcyScraper::url($id);
    }
}
