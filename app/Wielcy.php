<?php

namespace App;

use Carbon\CarbonInterval;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Spatie\Regex\Regex;

class Wielcy
{
    private $id;
    private $url;
    private $source;
    private $attributes = [];

    public function __construct($id)
    {
        $this->id = $id;
        $this->prepareSource();
        $this->runParsers();
    }

    public static function url($id)
    {
        return 'http://www.sejm-wielki.pl/s/?m=NG&t=PN&n='.$id;
    }

    private function prepareSource()
    {
        $this->setUrl();
        $this->getSource();
    }

    private function setUrl()
    {
        $this->url = self::url($this->id);
    }

    private function getSource()
    {
        $this->source = Cache::remember(
            "wielcy.$this->id",
            CarbonInterval::day(),
            fn () => iconv('iso-8859-2', 'UTF-8', file_get_contents($this->url))
        );
    }

    private function runParsers()
    {
        $this->parseName();
        $this->parseBio();
    }

    private function parseName()
    {
        $matches = Regex::match('/<h1[^<>]*><img src="images\/(female|male)\.png"[^<>]*\s*[^<>]*>([\s\S]+)<small>.*<\/small>\s*<\/h1>/', $this->source);
        if ($matches->hasMatch()) {
            //$matches = Arr::trim($matches);
            if ($matches->group(1) == 'female') {
                $this->sex = 'xx';
            } elseif ($matches->group(1) == 'male') {
                $this->sex = 'xy';
            }
            $this->name = Regex::replace('/<a[^<>]*>([^<>]*)<\/a>/', '<b>$1</b>', $matches->group(2))->result();
        }
    }

    private function parseBio()
    {
        $regex = '/<center>\s<table border="0" cellspacing="0" cellpadding="0">\s<tr><td><center>\s<em>\s([\s\S]+)<\/em><br>\s<\/center>\s<\/td><\/tr>\s<\/table>\s<\/center><p>/';
        if (preg_match($regex, $this->source, $matches) == 1) {
            $matches = Arr::trim($matches);
            if (filled($matches[1])) {
                $this->bio = $matches[1];
            }
        }
    }

    public function __get($key)
    {
        if (! $key) {
            return;
        }

        if ($key == 'id') {
            return $this->id;
        }

        if ($key == 'url') {
            return $this->url;
        }

        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }
}
