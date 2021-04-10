<?php

namespace App\Services\Wielcy;

use Carbon\CarbonInterval;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Spatie\Regex\Regex;

class Wielcy
{
    private ?string $source;

    private array $attributes = [];

    private array $keys = ['sex', 'name'];

    public function __construct(
        private string $id,
    ) {
        if ($this->getSource()) {
            $this->runParsers();
        }
    }

    private function getSource()
    {
        return $this->source = Cache::remember(
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
            },
        );
    }

    private function runParsers()
    {
        $this->parseName();
        $this->parseSex();
    }

    private function parseName()
    {
        $matches = Regex::match("/<meta property='og:title' content='([^']*)' \\/>/", $this->source);

        if (! $matches->hasMatch()) {
            return;
        }

        $this->attributes['name'] = $matches->group(1);
    }

    private function parseSex()
    {
        $matches = Regex::match("<img src=\"images/((?:fe)?male).png\" width=\"13\" height=\"13\"\nalt=\"M\" align=left>", $this->source);

        if (! $matches->hasMatch()) {
            return;
        }

        $this->attributes['sex'] = match ($matches->group(1)) {
            'male' => 'xy',
            'female' => 'xx',
            default => null,
        };
    }

    public static function url(string $id): string
    {
        return "http://www.sejm-wielki.pl/s/?m=NG&t=PN&n={$id}";
    }

    public function __get(string $key): mixed
    {
        if ($key === 'id') {
            return $this->id;
        }

        if ($key === 'url') {
            return self::url($this->id);
        }

        if (in_array($key, $this->keys)) {
            return $this->attributes[$key] ?? null;
        }

        throw new InvalidArgumentException("Key [{$key}] does not exist.");
    }
}
