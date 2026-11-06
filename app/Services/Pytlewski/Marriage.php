<?php

namespace App\Services\Pytlewski;

use App\Models\Person;

final class Marriage
{
    public ?Person $person {
        get => $this->repository->get($this->id);
    }

    public ?string $url {
        get => $this->id !== null ? PytlewskiScraper::url($this->id) : null;
    }

    public function __construct(
        private readonly RelativesRepository $repository,
        public readonly ?int $id,
        public readonly ?string $name,
        public readonly ?string $date,
        public readonly ?string $place,
    ) { }
}
