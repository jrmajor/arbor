<?php

namespace App\Services\Pytlewski;

use Carbon\CarbonInterval;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Pytlewski
{
    use Concerns\ScrapesPytlewski;

    private $id;
    private $source;
    private $attributes = [];

    public function __construct($id)
    {
        $this->id = $id;
        $source = $this->getSource($id);
        $this->attributes = $this->scrape($source);

        if (! isset($this->attributes['marriages'])) {
            $this->attributes['marriages'] = [];
        }

        if (! isset($this->attributes['children'])) {
            $this->attributes['children'] = [];
        }

        if (! isset($this->attributes['siblings'])) {
            $this->attributes['siblings'] = [];
        }
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

    public function hasParents(): bool
    {
        return isset($this->attributes['mother_surname']) || isset($this->attributes['father_surname'])
                || isset($this->attributes['mother_name']) || isset($this->attributes['father_name']);
    }

    public function hasMarriages(): bool
    {
        return is_array($this->marriages) && count($this->marriages) > 0;
    }

    public function hasChildren(): bool
    {
        return is_array($this->children) && count($this->children) > 0;
    }

    public function hasSiblings(): bool
    {
        return is_array($this->siblings) && count($this->siblings) > 0;
    }

    public static function url($id): string
    {
        return 'http://www.pytlewski.pl/index/drzewo/index.php?view=true&id='.$id;
    }

    private function getSource()
    {
        return Cache::remember(
            'pytlewski.'.$this->id,
            CarbonInterval::day(),
            fn () => $this->getSourceFromPytlewski($this->id)
        );
    }

    private function getSourceFromPytlewski()
    {
        try {
            $source = Http::timeout(2)->get(self::url($this->id));
        } catch (Exception $e) {
            return null;
        }

        if (! $source->ok()) {
            return null;
        }

        $source = iconv('Windows-1250', 'UTF-8', $source->body());

        $source = strstr($source, '<table id="metrzyczka" width="481" height="451" border="0" cellpadding="0" cellspacing="0">');

        $source = strstr($source, '<td background="images/spacer.gif" width="35" height="1"></td>', true);

        return $source;
    }
}
