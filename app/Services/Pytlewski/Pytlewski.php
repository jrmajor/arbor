<?php

namespace App\Services\Pytlewski;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Cache;

class Pytlewski
{
    use Concerns\ScrapesPytlewski;

    private $id;
    private $source;
    private $attributes = [];

    public function __construct($id)
    {
        $this->id = $id;

        $this->attributes = Cache::remember(
            'pytlewski.'.$this->id,
            CarbonInterval::week(),
            fn () => $this->scrape()
        );
    }

    public function hasParents(): bool
    {
        return isset($this->attributes['mother_surname']) || isset($this->attributes['father_surname'])
                || isset($this->attributes['mother_name']) || isset($this->attributes['father_name']);
    }

    public function hasMarriages(): bool
    {
        return count($this->marriages) > 0;
    }

    public function hasChildren(): bool
    {
        return count($this->children) > 0;
    }

    public function hasSiblings(): bool
    {
        return count($this->siblings) > 0;
    }

    public function __get($key)
    {
        if ($key == 'id') {
            return $this->id;
        }

        if ($key == 'url') {
            return self::url($this->id);
        }

        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
    }

    public static function url($id): string
    {
        return 'http://www.pytlewski.pl/index/drzewo/index.php?view=true&id='.$id;
    }
}
