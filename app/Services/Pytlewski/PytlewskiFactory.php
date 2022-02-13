<?php

namespace App\Services\Pytlewski;

use App\Enums\Sex;
use App\Models\Person;
use Carbon\CarbonInterval;
use Generator;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Psl\Fun;
use Psl\Html;
use Psl\Regex;
use Psl\Str;
use Psl\Vec;
use Symfony\Component\DomCrawler\Crawler;

use function App\nullable_trim;
use function App\parse_int;

final class PytlewskiFactory
{
    public function find(int $id): ?Pytlewski
    {
        if (null === $source = $this->getSource($id)) {
            return null;
        }

        try {
            $crawler = (new Crawler($source))->filter('#metrzyczka')->children();
        } catch (InvalidArgumentException) {
            return null;
        }

        $attributes = $this->scrapeAttributes($crawler);

        if (! $this->exists($attributes)) {
            return null;
        }

        $relations = $this->scrapeRelations($crawler, $relatives = new RelativesRepository());

        return tap(
            new Pytlewski($id, ...$attributes, ...$relations),
            fn (Pytlewski $pytlewski) => $relatives->initialize($pytlewski),
        );
    }

    public function for(Person $person): ?Pytlewski
    {
        if ($person->id_pytlewski === null) {
            return null;
        }

        return $this->find($person->id_pytlewski);
    }

    private function exists(array $attributes): bool
    {
        return isset($attributes['familyName'])
            || isset($attributes['lastName'])
            || isset($attributes['name']);
    }

    private function getSource(int $id): ?string
    {
        return Cache::remember(
            "pytlewski.{$id}",
            CarbonInterval::week(),
            function () use ($id): ?string {
                try {
                    $source = Http::timeout(2)->get(self::url($id));
                } catch (ConnectionException) {
                    return null;
                }

                if (! $source->ok()) {
                    return null;
                }

                return Fun\pipe(
                    fn ($s) => iconv('Windows-1250', 'UTF-8', $s) ?: '',
                    fn ($s) => Str\after($s, '<table border=0 align=center width=500><tr><td>') ?? '',
                    fn ($s) => Str\before($s, '<td background="images/spacer.gif" width="35" height="1"></td>'),
                )($source->body());
            },
        );
    }

    /**
     * @return array{
     *     familyName?: string, lastName?: string, name?: string, middleName?: string,
     *     birthDate?: string, birthPlace?: string,
     *     deathDate?: string, deathPlace?: string, burialPlace?: string,
     *     photo?: string, bio?: string
     * }
     */
    private function scrapeAttributes(Crawler $crawler): array
    {
        return Arr::trim([
            ...$this->parseNames($crawler),
            ...$this->parseDates($crawler),
            ...$this->parsePhoto($crawler),
            ...$this->parseBio($crawler),
        ]);
    }

    /**
     * @return array{familyName?: string, lastName?: ?string, name?: string, middleName?: string}
     */
    private function parseNames(Crawler $crawler): array
    {
        try {
            $names = $crawler->eq(1)
                ->children()->eq(1)
                ->children()->first()
                ->children()->first()
                ->children()->first()
                ->children()->first()
                ->children()->first()
                ->html();
        } catch (InvalidArgumentException) {
            return [];
        }

        [$surnames, $names] = Str\split($names, '<br>');

        $names = Str\split($names, '-');
        $matches = Regex\first_match($surnames, '/(.*) \\((.*)\\).*/');

        return [
            'familyName' => Html\strip_tags($matches ? $matches[2] : $surnames),
            'lastName' => $matches ? Html\strip_tags($matches[1]) : null,
            'name' => array_shift($names),
            'middleName' => Str\join($names, ' '),
        ];
    }

    /**
     * @return array{birthDate?: string, birthPlace?: string, deathDate?: string, deathPlace?: ?string, burialPlace?: ?string}
     */
    private function parseDates(Crawler $crawler): array
    {
        try {
            $dates = $crawler->eq(1)
                ->children()->eq(1)
                ->children()->first()
                ->children()->first()
                ->children()->first()
                ->children()->eq(2)
                ->html();
        } catch (InvalidArgumentException) {
            return [];
        }

        $attributes = [];

        $dates = Str\split($dates, '<br>');

        $matches = Regex\first_match($dates[0], '/ur\\. ([^ ]*) w ([^<]*)/');

        if ($matches !== null) {
            $attributes['birthDate'] = $matches[1];

            if (! Str\contains($matches[2], 'brak')) {
                $attributes['birthPlace'] = $matches[2];
            }
        }

        if (! isset($dates[1])) {
            return $attributes;
        }

        $pattern = '/\\(zm\\. ([^ ]*)(?: w ([^<),]*)(?:,poch\\.([^<)]*)?\\)?)?)?/';
        $matches = Regex\first_match($dates[1], $pattern);

        if ($matches !== null) {
            $attributes['deathDate'] = $matches[1];

            if (! Str\contains($matches[2] ?? '', 'brak')) {
                $attributes['deathPlace'] = $matches[2] ?? null;
            }

            $attributes['burialPlace'] = $matches[3] ?? null;
        }

        return $attributes;
    }

    /**
     * @return array{photo?: string}
     */
    private function parsePhoto(Crawler $crawler): array
    {
        try {
            $photo = $crawler->eq(2)->children()->eq(1)->filter('img')->attr('src');
        } catch (InvalidArgumentException) {
            return [];
        }

        return ['photo' => "http://www.pytlewski.pl/index/drzewo/{$photo}"];
    }

    /**
     * @return array{bio?: ?string}
     */
    private function parseBio(Crawler $crawler): array
    {
        try {
            $bio = $crawler->eq(5)->children()->eq(1)->text();
        } catch (InvalidArgumentException) {
            return [];
        }

        return ['bio' => $bio === 'pusto :(' ? null : $bio];
    }

    /**
     * @return array{
     *     familyName?: string, lastName?: string, name?: string, middleName?: string,
     *     birthDate?: string, birthPlace?: string,
     *     deathDate?: string, deathPlace?: string, burialPlace?: string,
     *     photo?: string, bio?: string
     * }
     */
    private function scrapeRelations(Crawler $crawler, RelativesRepository $relatives): array
    {
        return [
            'father' => $this->parseParent($crawler, $relatives, Sex::Male),
            'mother' => $this->parseParent($crawler, $relatives, Sex::Female),
            ...$this->parseRelations($crawler, $relatives),
        ];
    }

    private function parseParent(Crawler $crawler, RelativesRepository $relatives, Sex $type): ?Relative
    {
        try {
            $parents = $crawler->eq(1)
                ->children()->eq(1)
                ->children()->first()
                ->children()->eq(3)
                ->children()->first()
                ->children()->first()
                ->html();
        } catch (InvalidArgumentException) {
            return null;
        }

        $parent = match ($type) {
            Sex::Male => Str\split(Str\replace($parents, '-', ' '), '<br>')[1],
            Sex::Female => Str\split(Str\replace($parents, '-', ' '), '<br>')[0],
        };

        $names = explode(',', Html\strip_tags($parent));

        $id = parse_int(Regex\first_match($parent, '/id=([0-9]+)/')[1] ?? null);
        $name = nullable_trim(count($names) === 2 ? $names[1] : null);
        $surname = nullable_trim(count($names) === 2 ? $names[0] : Str\join($names, ' '));

        if ($name === null && $surname === null) {
            return null;
        }

        return new Relative($relatives, $id, $name, $surname);
    }

    private function parseRelations(Crawler $crawler, RelativesRepository $relatives): Generator
    {
        try {
            $crawler = $crawler->eq(3)
                ->children()->first()
                ->children()->first()
                ->children();
        } catch (InvalidArgumentException) {
            return;
        }

        try {
            $marriages = $crawler->eq(0)->children()->first()->html();

            yield 'marriages' => $this->parseMarriages($marriages, $relatives);
        } catch (InvalidArgumentException) { }

        try {
            $children = $crawler->eq(1)->children()->first()->html();

            yield 'children' => $this->parseChildrenOrSiblings($children, $relatives);
        } catch (InvalidArgumentException) { }

        try {
            $siblings = $crawler->eq(2)->children()->first()->html();

            yield 'siblings' => $this->parseChildrenOrSiblings($siblings, $relatives);
        } catch (InvalidArgumentException) { }
    }

    /**
     * @return list<Marriage>
     */
    private function parseMarriages(string $src, RelativesRepository $relatives): array
    {
        $pattern = '/(?:<u><a href=".*id=([0-9]*)">)?([^<>(]+)(?:<\\/a><\\/u>)? ?(?:\\(.*: ?([0-9.]*)(?:(?:,| )*([^)]*))?\\))?/';

        /** @phpstan-ignore-next-line */
        return Fun\pipe(
            fn (string $src) => Str\split($src, '</center>')[1],
            fn (string $src) => Str\split($src, '<br>'),
            fn (array $marriages) => Vec\map($marriages, fn (string $m) => Regex\first_match($m, $pattern)),
            fn (array $m) => Vec\filter($m, function (?array $match) {
                return $match !== null && ! Str\starts_with($match[2] ?? '', 'Nie zawar');
            }),
            fn (array $result) => Vec\map($result, fn (array $m) => new Marriage(
                $relatives,
                id: parse_int($m[1] ?? null),
                name: nullable_trim($m[2] ?? null),
                date: nullable_trim($m[3] ?? null),
                place: nullable_trim($m[4] ?? null),
            )),
        )($src);
    }

    /**
     * @return list<Marriage>
     */
    private function parseChildrenOrSiblings(string $src, RelativesRepository $relatives): array
    {
        $pattern = '/(?:<u><a href=".*id=([0-9]*)">)?([^<>]*)/';

        /** @phpstan-ignore-next-line */
        return Fun\pipe(
            fn (string $src) => Str\split($src, '</center>')[1],
            fn (string $src) => Str\split($src, '; '),
            fn (array $children) => Vec\map($children, fn (string $c) => Regex\first_match($c, $pattern)),
            fn (array $c) => Vec\filter($c, function (?array $match) {
                return $match !== null && ! Str\starts_with($match[2] ?? '', 'Nie ma');
            }),
            fn (array $result) => Vec\map($result, fn (array $c) => new Relative(
                $relatives,
                id: parse_int($c[1] ?? null),
                name: $c[2] ?? null,
            )),
        )($src);
    }

    public static function url(int $id): string
    {
        return "http://www.pytlewski.pl/index/drzewo/index.php?view=true&id={$id}";
    }
}
