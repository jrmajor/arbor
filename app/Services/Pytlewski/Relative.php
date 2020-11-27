<?php

namespace App\Services\Pytlewski;

class Relative
{
    protected array $keys = [
        'id', 'person',
        'name', 'surname',
    ];

    public static function hydrate(array $attributes)
    {
        return new static($attributes);
    }

    public function __construct(
        protected array $attributes
    ) {}

    public function __get($key)
    {
        if ($key === 'url') {
            return $this->attributes['id'] ?? null
                ? Pytlewski::url($this->attributes['id'])
                : null;
        }

        if (in_array($key, $this->keys)) {
            return $this->attributes[$key] ?? null;
        }
    }
}
