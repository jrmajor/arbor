<?php

namespace App\Services\Pytlewski;

use App\Models\Person;
use Exception;
use Psl\Str;

final class Marriage
{
    /** @phpstan-ignore-next-line It's lazily initialized by __get. */
    public readonly ?Person $person;

    public readonly ?string $url;

    public function __construct(
        private readonly RelativesRepository $repository,
        public readonly ?int $id,
        public readonly ?string $name,
        public readonly ?string $date,
        public readonly ?string $place,
    ) {
        unset($this->person);

        $this->url = $id !== null ? PytlewskiFactory::url($id) : null;
    }

    public function __get(string $name): ?Person
    {
        if ($name !== 'person') {
            throw new Exception(Str\format('Undefined property: %s::$%s', self::class, $name));
        }

        return $this->repository->get($this->id);
    }
}
