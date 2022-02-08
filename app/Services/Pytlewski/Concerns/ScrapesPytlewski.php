<?php

namespace App\Services\Pytlewski\Concerns;

use Generator;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Psl\Fun;
use Psl\Html;
use Psl\Regex;
use Psl\Str;
use Symfony\Component\DomCrawler\Crawler;

trait ScrapesPytlewski
{
    private function scrape(): array
    {
        $source = $this->getSource();

        if ($source === null) {
            return [];
        }

        try {
            $crawler = (new Crawler($source))
                ->filter('#metrzyczka')
                ->children();
        } catch (InvalidArgumentException) {
            return [];
        }

        $attributes = Arr::trim([
            ...$this->parseNames($crawler),
            ...$this->parseDates($crawler),
            ...$this->parseParents($crawler),
            ...$this->parsePhoto($crawler),
            ...$this->parseRelations($crawler),
            ...$this->parseBio($crawler),
        ]);

        foreach (['marriages', 'children', 'siblings'] as $key) {
            $attributes[$key] ??= [];
        }

        return $attributes;
    }

    private function getSource(): ?string
    {
        try {
            $source = Http::timeout(2)->get(self::url($this->id));
        } catch (ConnectionException) {
            return null;
        }

        if (! $source->ok()) {
            return null;
        }

        return Fun\pipe(
            fn ($s) => iconv('Windows-1250', 'UTF-8', $s) ?: '',
            fn ($s) => Str\after($source, '<table border=0 align=center width=500><tr><td>') ?? '',
            fn ($s) => Str\before($source, '<td background="images/spacer.gif" width="35" height="1"></td>'),
        )($source->body());
    }

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

        $matches = Regex\first_match($surnames, '/(.*) \\((.*)\\).*/');

        $attributes = match ($matches !== null) {
            true => [
                'last_name' => Html\strip_tags($matches[1]),
                'family_name' => Html\strip_tags($matches[2]),
            ],
            false => ['family_name' => Html\strip_tags($surnames)],
        };

        $names = Str\split($names, '-');

        return [
            'name' => array_shift($names),
            'middle_name' => Str\join($names, ' '),
            ...$attributes,
        ];
    }

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
            $attributes['birth_date'] = $matches[1];

            if (! Str\contains($matches[2], 'brak')) {
                $attributes['birth_place'] = $matches[2];
            }
        }

        if (! isset($dates[1])) {
            return $attributes;
        }

        $matches = Regex\first_match(
            $dates[1],
            '/\\(zm\\. ([^ ]*)(?: w ([^<),]*)(?:,poch\\.([^<)]*)?\\)?)?)?/',
        );

        if ($matches !== null) {
            $attributes['death_date'] = $matches[1];

            if (! Str\contains($matches[2] ?? '', 'brak')) {
                $attributes['death_place'] = $matches[2] ?? null;
            }

            $attributes['burial_place'] = $matches[3] ?? null;
        }

        return $attributes;
    }

    private function parseParents(Crawler $crawler): array
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
            return [];
        }

        [$mother, $father] = Str\split(Str\replace($parents, '-', ' '), '<br>');

        $attributes = [
            'mother_id' => Regex\first_match($mother, '/id=([0-9]+)/')[1] ?? null,
            'father_id' => Regex\first_match($father, '/id=([0-9]+)/')[1] ?? null,
        ];

        $mother = explode(',', Html\strip_tags($mother));

        if (count($mother) === 2) {
            [$attributes['mother_surname'], $attributes['mother_name']] = $mother;
        } else {
            $attributes['mother_surname'] = Str\join($mother, ' ');
        }

        $father = explode(',', Html\strip_tags($father));

        if (count($father) === 2) {
            [$attributes['father_surname'], $attributes['father_name']] = $father;
        } else {
            $attributes['father_surname'] = Str\join($father, ' ');
        }

        return $attributes;
    }

    private function parsePhoto(Crawler $crawler): array
    {
        try {
            $photo = $crawler->eq(2)->children()->eq(1)->filter('img')->attr('src');
        } catch (InvalidArgumentException) {
            return [];
        }

        return ['photo' => "http://www.pytlewski.pl/index/drzewo/{$photo}"];
    }

    private function parseRelations(Crawler $crawler): Generator
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
            yield 'marriages' => $this->parseMarriages(
                $crawler->eq(0)->children()->first()->html(),
            );
        } catch (InvalidArgumentException) { }

        try {
            yield 'children' => $this->parseChildrenOrSiblings(
                $crawler->eq(1)->children()->first()->html(),
            );
        } catch (InvalidArgumentException) { }

        try {
            yield 'siblings' => $this->parseChildrenOrSiblings(
                $crawler->eq(2)->children()->first()->html(),
            );
        } catch (InvalidArgumentException) { }
    }

    /**
     * @return Collection<int, array{id: string, name: string, date: string, place: string}>
     */
    private function parseMarriages(string $marriages): Collection
    {
        $marriages = Str\split($marriages, '</center>')[1];
        $marriages = Str\split($marriages, '<br>');

        return collect($marriages)
            ->map(fn (string $marriage) => Regex\first_match(
                $marriage,
                '/(?:<u><a href=".*id=([0-9]*)">)?([^<>(]+)(?:<\\/a><\\/u>)? ?(?:\\(.*: ?([0-9.]*)(?:(?:,| )*([^)]*))?\\))?/',
            ))
            ->reject(function (?array $result) {
                return $result === null
                    || Str\starts_with($result[2] ?? '', 'Nie zawar');
            })
            ->map(fn (array $result) => [
                'id' => $result[1] ?? null,
                'name' => $result[2] ?? null,
                'date' => $result[3] ?? null,
                'place' => $result[4] ?? null,
            ]);
    }

    /**
     * @return Collection<int, array{id: string, name: string}>
     */
    private function parseChildrenOrSiblings(string $children): Collection
    {
        $children = Str\split($children, '</center>')[1];
        $children = Str\split($children, '; ');

        return collect($children)
            ->map(fn (string $child) => Regex\first_match(
                $child,
                '/(?:<u><a href=".*id=([0-9]*)">)?([^<>]*)/',
            ))
            ->reject(function (?array $result) {
                return $result === null
                    || Str\starts_with($result[2] ?? '', 'Nie ma');
            })
            ->map(fn (array $result) => [
                'id' => $result[1] ?? null,
                'name' => $result[2] ?? null,
            ]);
    }

    private function parseBio(Crawler $crawler): array
    {
        try {
            $bio = $crawler->eq(5)->children()->eq(1)->text();
        } catch (InvalidArgumentException) {
            return [];
        }

        return ['bio' => $bio === 'pusto :(' ? null : $bio];
    }
}
