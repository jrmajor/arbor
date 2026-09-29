<?php

namespace App\Services\Wielcy;

use App\Enums\Sex;
use App\Models\Person;
use Carbon\CarbonInterval;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Psl\Dict;
use Psl\Iter;
use Psl\Regex;
use Psl\Str;
use Psl\Vec;
use Symfony\Component\DomCrawler\Crawler;

use function App\nullable_trim;
use function App\trim_values;

final class WielcyScraper
{
    public function find(string $id): ?Wielcy
    {
        if (null === $source = $this->getSource($id)) {
            return null;
        }

        // the source is already converted to UTF-8, but it still declares ISO-8859-2
        $crawler = new Crawler();
        $crawler->addHtmlContent($source, 'UTF-8');

        $attributes = $this->scrapeAttributes($crawler, $id);

        if (! $this->exists($attributes)) {
            return null;
        }

        $relations = $this->scrapeRelations($crawler, $relatives = new RelativesRepository());

        return tap(
            new Wielcy($id, ...[...$attributes, ...$relations]),
            fn (Wielcy $wielcy) => $relatives->initialize($wielcy),
        );
    }

    public function for(Person $person): ?Wielcy
    {
        if (! $person->id_wielcy) {
            return null;
        }

        return $this->find($person->id_wielcy);
    }

    /**
     * @param array<string, ?string> $attributes
     */
    private function exists(array $attributes): bool
    {
        return isset($attributes['surname'])
            || isset($attributes['name']);
    }

    private function getSource(string $id): ?string
    {
        return Cache::flexible(
            "wielcy.{$id}",
            [CarbonInterval::day(), CarbonInterval::year()],
            function () use ($id): ?string {
                try {
                    $source = Http::timeout(2)->get(self::url($id));
                } catch (ConnectionException) {
                    return null;
                }

                if (! $source->ok()) {
                    return null;
                }

                // every byte is valid in ISO-8859-2, so this can't fail
                $source = iconv('ISO-8859-2', 'UTF-8', $source->body());

                return $source !== false ? $source : null;
            },
        );
    }

    /**
     * @return array{
     *     name: ?string, middleName: ?string, surname: ?string, sex: 'xy'|'xx'|null,
     *     birthDate: ?string, birthPlace: ?string,
     *     deathDate: ?string, deathPlace: ?string, burialPlace: ?string,
     *     photo: ?string
     * }
     */
    private function scrapeAttributes(Crawler $crawler, string $id): array
    {
        return [
            ...$this->parseNames($crawler),
            'sex' => $this->parseSex($crawler),
            ...$this->parseDates($crawler),
            'photo' => $this->parsePhoto($crawler, $id),
        ];
    }

    /**
     * @return array{name: ?string, middleName: ?string, surname: ?string}
     */
    private function parseNames(Crawler $crawler): array
    {
        try {
            $heading = $crawler->filter('h1')->text();
        } catch (InvalidArgumentException) {
            return Dict\from_keys(['name', 'middleName', 'surname'], fn () => null);
        }

        try {
            $surname = $crawler->filter('h1 > a')->text();
        } catch (InvalidArgumentException) {
            $surname = null;
        }

        $names = $surname !== null
            ? Str\before($heading, $surname)
            : Str\before($heading, '(ID:');

        $names = Str\split(Str\trim($names ?? ''), ' ', 2);

        return trim_values([
            'name' => $names[0],
            'middleName' => $names[1] ?? null,
            'surname' => $surname,
        ]);
    }

    /**
     * @return 'xy'|'xx'|null
     */
    private function parseSex(Crawler $crawler): ?string
    {
        try {
            $icon = $crawler->filter('h1 > img')->attr('src');
        } catch (InvalidArgumentException) {
            return null;
        }

        return match ($icon) {
            'images/male.png' => 'xy',
            'images/female.png' => 'xx',
            default => null,
        };
    }

    /**
     * @return array{birthDate: ?string, birthPlace: ?string, deathDate: ?string, deathPlace: ?string, burialPlace: ?string}
     */
    private function parseDates(Crawler $crawler): array
    {
        $attributes = ['birthDate', 'birthPlace', 'deathDate', 'deathPlace', 'burialPlace'];
        $attributes = Dict\from_keys($attributes, fn () => null);

        try {
            $facts = $crawler->filter('.lewa-szpalta ul')->first()->children('li');
        } catch (InvalidArgumentException) {
            return $attributes;
        }

        foreach ($facts as $fact) {
            $fact = Str\replace(new Crawler($fact)->text(), "\u{A0}", ' ');

            if (null !== $matches = Regex\first_match($fact, '/^Urodzon[ya]\\b(.*)$/u')) {
                [$attributes['birthDate'], $attributes['birthPlace']] = $this->splitDatePlace($matches[1]);
            } elseif (null !== $matches = Regex\first_match($fact, '/^zmarła?\\b(.*)$/iu')) {
                [$attributes['deathDate'], $attributes['deathPlace']] = $this->splitDatePlace($matches[1]);
            } elseif (null !== $matches = Regex\first_match($fact, '/^Pochowan[ya]\\b(.*)$/u')) {
                $attributes['burialPlace'] = $this->splitDatePlace($matches[1])[1];
            }
        }

        return trim_values($attributes);
    }

    /**
     * Splits facts like "dnia 1 IV 1878 - Zaleszczyki" or "w roku 1855".
     *
     * @return array{?string, ?string}
     */
    private function splitDatePlace(string $fact): array
    {
        // places can contain hyphens (Bielsko-Biała), but not followed by a space
        $parts = Str\split(" {$fact}", ' - ', 2);

        $date = Regex\replace($parts[0], '/^\\s*dnia\\b/u', '');

        return [nullable_trim($date), nullable_trim($parts[1] ?? null)];
    }

    private function parsePhoto(Crawler $crawler, string $id): ?string
    {
        // photos of relatives are shown too, their keys contain the ids of people they show
        foreach ($crawler->filter('.lewa-szpalta img[alt="ilustracja"]') as $image) {
            $src = new Crawler($image)->attr('src') ?? '';

            if (Str\contains($src, "__{$id}.")) {
                return "https://www.sejm-wielki.pl/{$src}";
            }
        }

        return null;
    }

    /**
     * @return array{father: ?Relative, mother: ?Relative}
     */
    private function scrapeRelations(Crawler $crawler, RelativesRepository $relatives): array
    {
        $table = $crawler->filterXPath('//h3[normalize-space(.)="Rodzice"]/ancestor::table[1]');

        return [
            'father' => $this->parseParent($table, $relatives, Sex::Male),
            'mother' => $this->parseParent($table, $relatives, Sex::Female),
        ];
    }

    /**
     * Names of relatives are available only to subscribers, but keys of their photos contain
     * lowercased names and ids. People with public pages are also linked with proper names.
     */
    private function parseParent(Crawler $table, RelativesRepository $relatives, Sex $type): ?Relative
    {
        $column = match ($type) {
            Sex::Male => 1,
            Sex::Female => 3,
        };

        try {
            $photo = $table->filter("tr:nth-child(1) > td:nth-child({$column}) img")->attr('src');
        } catch (InvalidArgumentException) {
            $photo = null;
        }

        try {
            $link = $table->filter("tr:nth-child(2) > td:nth-child({$column}) a[href^=\"/b/\"]");
            $link = ['id' => Str\after($link->attr('href') ?? '', '/b/'), 'name' => $link->text()];
        } catch (InvalidArgumentException) {
            $link = null;
        }

        $key = $this->parsePhotoKey($photo ?? '');

        if ($link === null && $key === null) {
            return null;
        }

        if ($link === null) {
            return new Relative($relatives, $key['id'], $key['name'], $key['surname']);
        }

        // the key tells how many words of the name are given names
        $words = Str\split($link['name'], ' ');
        $count = $key !== null ? count(Str\split($key['name'] ?? '', ' ')) : 0;

        return new Relative(
            $relatives,
            nullable_trim($link['id']),
            nullable_trim(Str\join(Vec\take($words, $count), ' ')),
            nullable_trim(Str\join(Vec\drop($words, $count), ' ')),
        );
    }

    /**
     * Parses keys like "leodgard__psb.6305.2.0.gąsiorowski" or "zygmunt_leon_cz.i017789.0.hr._komorowski".
     *
     * @return ?array{id: ?string, name: ?string, surname: ?string}
     */
    private function parsePhotoKey(string $src): ?array
    {
        $matches = Regex\first_match($src, '/k=\\/(.+?)_{1,2}([a-z]+(?:\\.[^._]+)+?)\\.\\d+\\.(\\D.*)$/iu');

        if ($matches === null) {
            return null;
        }

        [, $name, $id, $surname] = $matches;

        return [
            // keys are lowercased, and ids with letters (like cz.I017795) don't work in lowercase
            'id' => Regex\matches($id, '/^[a-z]+(\\.\\d+)+$/') ? $id : null,
            'name' => nullable_trim(self::capitalize(Str\replace($name, '_', ' '))),
            // "n." stands for unknown surname
            'surname' => $surname === 'n.' ? null : nullable_trim(self::capitalize(Str\replace($surname, '_', ' '))),
        ];
    }

    /**
     * Capitalizes words, except for particles and abbreviations like "de", "z" or "h.".
     */
    private static function capitalize(string $words): string
    {
        return Str\join(Vec\map(
            Str\split($words, ' '),
            fn (string $word) => Str\ends_with($word, '.') || Iter\contains(['de', 'von', 'van', 'z', 'ze'], $word)
                ? $word
                : Str\capitalize($word),
        ), ' ');
    }

    public static function url(string $id): string
    {
        return "https://www.sejm-wielki.pl/s/?m=NG&t=PN&n={$id}";
    }
}
