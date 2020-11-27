<?php

namespace App\Services\Wielcy;

use Carbon\CarbonInterval;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Spatie\Regex\Regex;

class Wielcy
{
    private ?string $source;

    private array $attributes = [];

    public function __construct(
        private string $id
    ) {
        $this->getSource();
        $this->runParsers();
    }

    private function getSource()
    {
        $this->source = Cache::remember(
            "wielcy.{$this->id}",
            CarbonInterval::day(),
            function (): ?string {
                try {
                    $source = Http::timeout(2)->get(self::url($this->id));
                } catch (ConnectionException) {
                    return null;
                }

                if (! $source->ok()) {
                    return null;
                }

                $source = iconv('iso-8859-2', 'UTF-8', $source->body());

                return $source !== false ? $source : null;
            }
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
            if ($matches->group(1) === 'female') {
                $this->sex = 'xx';
            } elseif ($matches->group(1) === 'male') {
                $this->sex = 'xy';
            }

            $this->name = Regex::replace('/<a[^<>]*>([^<>]*)<\/a>/', '<b>$1</b>', $matches->group(2))->result();
        }
    }

    private function parseBio()
    {
        $regex = '/<center>\s<table border="0" cellspacing="0" cellpadding="0">\s<tr><td><center>\s<em>\s([\s\S]+)<\/em><br>\s<\/center>\s<\/td><\/tr>\s<\/table>\s<\/center><p>/';

        if (preg_match($regex, $this->source, $matches) === 1) {
            $matches = Arr::trim($matches);

            if (filled($matches[1])) {
                $this->bio = $matches[1];
            }
        }
    }

    public function __get(string $key): mixed
    {
        if ($key === 'id') {
            return $this->id;
        }

        if ($key === 'url') {
            return self::url($this->id);
        }

        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        throw new InvalidArgumentException("Key [{$key}] does not exist.");
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public static function url(string $id): string
    {
        return 'http://www.sejm-wielki.pl/s/?m=NG&t=PN&n='.$id;
    }
}
