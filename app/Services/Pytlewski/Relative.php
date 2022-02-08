<?php

namespace App\Services\Pytlewski;

use App\Models\Person;
use InvalidArgumentException;

/**
 * @property-read ?string $id
 * @property-read ?string $name
 * @property-read ?string $surname
 * @property-read ?Person $person
 */
class Relative
{
    protected array $keys = ['id', 'name', 'surname', 'person'];

    final public function __construct(
        protected array $attributes,
    ) { }

    final public static function hydrate(array $attributes): static
    {
        return new static($attributes);
    }

    final public function __get(string $key): mixed
    {
        if ($key === 'url') {
            return $this->attributes['id'] ?? null
                ? Pytlewski::url($this->attributes['id'])
                : null;
        }

        if (in_array($key, $this->keys)) {
            return $this->attributes[$key] ?? null;
        }

        throw new InvalidArgumentException("Key [{$key}] does not exist.");
    }
}
