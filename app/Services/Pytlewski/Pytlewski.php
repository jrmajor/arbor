<?php

namespace App\Services\Pytlewski;

use Carbon\CarbonInterval;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Pytlewski
{
    use Traits\ScrapesPytlewski;

    private $id;
    private $source;
    private $attributes = [];

    public function __construct($id)
    {
        $this->id = $id;
        $source = $this->getSource($id);
        $this->attributes = $this->runParsers($source);
    }

    private function runParsers($source): array
    {
        if ($source === null) {
            return [];
        }

        $attributes = $this->getParsers()
        ->flatMap(
            fn ($method) => $this->{$method}($source)
        );

        return Arr::trim($attributes->toArray());
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

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
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
