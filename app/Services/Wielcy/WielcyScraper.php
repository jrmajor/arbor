<?php

namespace App\Services\Wielcy;

use App\Models\Person;
use Carbon\CarbonInterval;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Psl\Regex;

final class WielcyScraper
{
    public function find(string $id): ?Wielcy
    {
        if (null === $source = $this->getSource($id)) {
            return null;
        }

        return new Wielcy(
            $id,
            name: $this->parseName($source),
            sex: $this->parseSex($source),
        );
    }

    public function for(Person $person): ?Wielcy
    {
        if (! $person->id_wielcy) {
            return null;
        }

        return $this->find($person->id_wielcy);
    }

    private function getSource(string $id): ?string
    {
        return Cache::flexible(
            "wielcy.{$id}",
            [CarbonInterval::day(), CarbonInterval::year()],
            function () use ($id): ?string {
                try {
                    $source = Http::timeout(2)
                        // todo: fix SSL issue
                        ->withoutVerifying()
                        ->get(self::url($id));
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

    private function parseName(string $source): ?string
    {
        $matches = Regex\first_match($source, "/<meta property='og:title' content='([^']*)' \\/>/");

        return $matches[1] ?? null;
    }

    /**
     * @return 'xy'|'xx'|null
     */
    private function parseSex(string $source): ?string
    {
        $matches = Regex\first_match($source, "<img src=\"images/((?:fe)?male).png\" width=\"13\" height=\"13\"\nalt=\"M\" align=left>");

        return match ($matches[1] ?? null) {
            'male' => 'xy',
            'female' => 'xx',
            default => null,
        };
    }

    public static function url(string $id): string
    {
        return "http://www.sejm-wielki.pl/s/?m=NG&t=PN&n={$id}";
    }
}
