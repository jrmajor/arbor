<?php

namespace App\Services\Wielcy;

use App\Models\Person;

final class Relative
{
    public ?Person $person {
        get => $this->repository->get($this->id);
    }

    public ?string $url {
        get => $this->id !== null ? WielcyScraper::url($this->id) : null;
    }

    public function __construct(
        private readonly RelativesRepository $repository,
        public readonly ?string $id,
        public readonly ?string $name,
        public readonly ?string $surname = null,
    ) { }
}
