<?php

namespace App\Services\Pytlewski;

class Relative
{
    protected $attributes;

    protected $keys = [
        'id', 'person',
        'name', 'surname',
    ];

    public static function hydrate(array $attributes)
    {
        return new static($attributes);
    }

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function __get($key)
    {
        if ($key == 'url') {
            return $this->attributes['id'] ?? null
                ? Pytlewski::url($this->attributes['id'])
                : null;
        }

        if (in_array($key, $this->keys)) {
            return $this->attributes[$key] ?? null;
        }
    }
}
