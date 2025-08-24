<?php

namespace App\Services\Wielcy;

use Carbon\CarbonInterval;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Psl\Iter;
use Psl\Regex;

/**
 * @property-read string $id
 * @property-read string $url
 * @property-read ?string $name
 * @property-read 'xy'|'xx'|null $sex
 */
final class Wielcy
{
    private ?string $source;

    /** @var array<string, mixed> */
    private array $attributes = [];

    /** @var list<string> */
    private array $keys = ['sex', 'name'];

    public function __construct(
        private string $id,
    ) {
        if ($this->getSource()) {
            $this->runParsers();
        }
    }

    private function getSource(): ?string
    {
        return $this->source = Cache::flexible(
            "wielcy.{$this->id}",
            [CarbonInterval::day(), CarbonInterval::year()],
            function (): ?string {
                try {
                    $source = Http::timeout(2)
                        // todo: fix SSL issue
                        ->withoutVerifying()
                        ->get(self::url($this->id));
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

    private function runParsers(): void
    {
        $this->parseName();
        $this->parseSex();
    }

    private function parseName(): void
    {
        $matches = Regex\first_match($this->source, "/<meta property='og:title' content='([^']*)' \\/>/");

        $this->attributes['name'] = $matches[1] ?? null;
    }

    private function parseSex(): void
    {
        $matches = Regex\first_match($this->source, "<img src=\"images/((?:fe)?male).png\" width=\"13\" height=\"13\"\nalt=\"M\" align=left>");

        $this->attributes['sex'] = match ($matches[1] ?? null) {
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
        return match (true) {
            $key === 'id' => $this->id,
            $key === 'url' => self::url($this->id),
            Iter\contains($this->keys, $key) => $this->attributes[$key] ?? null,
            default => throw new InvalidArgumentException("Key [{$key}] does not exist."),
        };
    }
}
