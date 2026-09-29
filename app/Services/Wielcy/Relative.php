<?php

namespace App\Services\Wielcy;

use App\Models\Person;
use Exception;
use Psl\Str;

final class Relative
{
    /** @phpstan-ignore property.uninitializedReadonly (it's lazily initialized by __get) */
    public readonly ?Person $person;

    public readonly ?string $url;

    public function __construct(
        private readonly RelativesRepository $repository,
        public readonly ?string $id,
        public readonly ?string $name,
        public readonly ?string $surname = null,
    ) {
        /** @phpstan-ignore property.uninitializedReadonly, unset.readOnlyProperty */
        unset($this->person);

        $this->url = $id !== null ? WielcyScraper::url($id) : null;
    }

    public function __get(string $name): ?Person
    {
        if ($name !== 'person') {
            throw new Exception(Str\format('Undefined property: %s::$%s', self::class, $name));
        }

        return $this->repository->get($this->id);
    }
}
