<?php

namespace App\Services\Wielcy;

final class Wielcy
{
    public readonly string $url;

    public function __construct(
        public readonly string $id,
        public readonly ?string $name = null,
        /** @var 'xy'|'xx'|null */
        public readonly ?string $sex = null,
    ) {
        $this->url = WielcyScraper::url($id);
    }
}
