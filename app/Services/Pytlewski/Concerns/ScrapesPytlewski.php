<?php

namespace App\Services\Pytlewski\Concerns;

use Generator;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Spatie\Regex\MatchResult;
use Spatie\Regex\Regex;
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

        $attributes = collect()
            ->merge($this->parseNames($crawler))
            ->merge($this->parseDates($crawler))
            ->merge($this->parseParents($crawler))
            ->merge($this->parsePhoto($crawler))
            ->merge($this->parseRelations($crawler))
            ->merge($this->parseBio($crawler))
            ->trim()
            ->toArray();

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

        $source = iconv('Windows-1250', 'UTF-8', $source->body());

        $source = strstr($source, '<table id="metrzyczka" width="481" height="451" border="0" cellpadding="0" cellspacing="0">');
        $source = strstr($source, '<td background="images/spacer.gif" width="35" height="1"></td>', true);

        return $source !== false ? $source : null;
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

        [$surnames, $names] = explode('<br>', $names);

        $matches = Regex::match('/(.*) \\((.*)\\).*/', $surnames);

        $attributes = match ($matches->hasMatch()) {
            true => [
                'last_name' => strip_tags($matches->group(1)),
                'family_name' => strip_tags($matches->group(2)),
            ],
            false => ['family_name' => strip_tags($surnames)],
        };

        $names = explode('-', $names);

        return array_merge([
            'name' => array_shift($names),
            'middle_name' => implode(' ', $names),
        ], $attributes);
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

        $dates = explode('<br>', $dates);

        $matches = Regex::match('/ur\\. ([^ ]*) w ([^<]*)/', $dates[0]);

        if ($matches->hasMatch()) {
            $attributes['birth_date'] = $matches->group(1);

            if (! str_contains($matches->group(2), 'brak')) {
                $attributes['birth_place'] = $matches->group(2);
            }
        }

        if (! isset($dates[1])) {
            return $attributes;
        }

        $matches = Regex::match('/\\(zm\\. ([^ ]*)(?: w ([^<),]*)(?:,poch\\.([^<)]*)?\\)?)?)?/', $dates[1]);

        if ($matches->hasMatch()) {
            $attributes['death_date'] = $matches->group(1);

            if (! str_contains($matches->groupOr(2, ''), 'brak')) {
                $attributes['death_place'] = $matches->groupOr(2, '');
            }

            $attributes['burial_place'] = $matches->groupOr(3, '');
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

        [$mother, $father] = explode('<br>', str_replace('-', ' ', $parents));

        $attributes = [
            'mother_id' => Regex::match('/id=([0-9]+)/', $mother)->groupOr(1, ''),
            'father_id' => Regex::match('/id=([0-9]+)/', $father)->groupOr(1, ''),
        ];

        $mother = explode(',', strip_tags($mother));

        if (count($mother) === 2) {
            [$attributes['mother_surname'], $attributes['mother_name']] = $mother;
        } else {
            $attributes['mother_surname'] = implode(' ', $mother);
        }

        $father = explode(',', strip_tags($father));

        if (count($father) === 2) {
            [$attributes['father_surname'], $attributes['father_name']] = $father;
        } else {
            $attributes['father_surname'] = implode(' ', $father);
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
        $marriages = explode('</center>', $marriages)[1];
        $marriages = explode('<br>', $marriages);

        return collect($marriages)
            ->map(fn (string $marriage) => Regex::match('/(?:<u><a href=".*id=([0-9]*)">)?([^<>(]+)(?:<\\/a><\\/u>)? ?(?:\\(.*: ?([0-9.]*)(?:(?:,| )*([^)]*))?\\))?/', $marriage))
            ->reject(function (MatchResult $result) {
                return ! $result->hasMatch()
                    || str_starts_with($result->groupOr(2, ''), 'Nie zawar');
            })
            ->map(fn (MatchResult $result) => [
                'id' => $result->groupOr(1, ''),
                'name' => $result->groupOr(2, ''),
                'date' => $result->groupOr(3, ''),
                'place' => $result->groupOr(4, ''),
            ]);
    }

    /**
     * @return Collection<int, array{id: string, name: string}>
     */
    private function parseChildrenOrSiblings(string $children): Collection
    {
        $children = explode('</center>', $children)[1];
        $children = explode('; ', $children);

        return collect($children)
            ->map(fn (string $child) => Regex::match('/(?:<u><a href=".*id=([0-9]*)">)?([^<>]*)/', $child))
            ->reject(function (MatchResult $result) {
                return ! $result->hasMatch()
                    || str_starts_with($result->groupOr(2, ''), 'Nie ma');
            })
            ->map(fn (MatchResult $result) => [
                'id' => $result->groupOr(1, ''),
                'name' => $result->groupOr(2, ''),
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
