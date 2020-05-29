<?php

namespace App\Services\Pytlewski\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ReflectionClass;
use Spatie\Regex\Regex;
use Symfony\Component\DomCrawler\Crawler;

trait ScrapesPytlewski
{
    private function scrape($source): array
    {
        if ($source === null) {
            return [];
        }

        try {

            $crawler = (new Crawler($source))
                ->filter('#metrzyczka')
                ->children();

            return Arr::trim(
                collect($this->parseBasicInfo($crawler->eq(1)->children()->eq(1)))
                ->merge($this->parsePhoto($crawler->eq(2)->children()->eq(1)))
                ->merge($this->parseRelations($crawler->eq(3)->children()->eq(0)))
                ->merge($this->parseBio($crawler->eq(5)->children()->eq(1)))
            )->toArray();

        } catch (InvalidArgumentException $e) {
            return [];
        }
    }

    private function parseBasicInfo(Crawler $crawler): array
    {
        $crawler = $crawler->children()->first()->children();

        $attr = [];

        $names = $crawler->eq(0)
                    ->children()->first()
                    ->children()->eq(0)
                    ->children()->first()
                    ->html();

        [$surnames, $names] = explode('<br>', $names);

        $matches = Regex::match('/(.*) \((.*)\).*/', $surnames);

        if ($matches->hasMatch()) {
            $attr['last_name'] = strip_tags($matches->group(1));
            $attr['family_name'] = strip_tags($matches->group(2));
        } else {
            $attr['family_name'] = strip_tags($surnames);
        }

        $names = explode('-', $names);

        $attr['name'] = $names[0];

        unset($names[0]);

        $attr['middle_name'] = implode(' ', $names);

        $dates = $crawler->eq(0)
                    ->children()->first()
                    ->children()->eq(2)
                    ->html();

        $dates = explode('<br>', $dates);

        $matches = Regex::match('/ur\. ([^ ]*) w ([^<]*)/', $dates[0]);

        if ($matches->hasMatch()) {
            $attr['birth_date'] = $matches->group(1);

            if (! Str::contains($matches->group(2), 'brak')) {
                $attr['birth_place'] = $matches->group(2);
            }
        }

        if (isset($dates[1])) {
            $matches = Regex::match('/\(zm\. ([^ ]*)(?: w ([^<),]*)(?:,poch\.([^<)]*)?\)?)?)?/', $dates[1]);

            if ($matches->hasMatch()) {
                $attr['death_date'] = $matches->group(1);

                if (! Str::contains($matches->groupOr(2, ''), 'brak')) {
                    $attr['death_place'] = $matches->groupOr(2, '');
                }

                $attr['burial_place'] = $matches->groupOr(3, '');
            }
        }

        $parents = $crawler->eq(3)
                    ->children()->first()
                    ->children()->first()
                    ->html();

        [$mother, $father] = explode('<br>', str_replace('-', ' ', $parents));

        $matches = Regex::match('/id=([0-9]+)/', $mother);
        if($matches->hasMatch()) {
            $attr['mother_id'] = $matches->group(1);
        }

        $matches = Regex::match('/id=([0-9]+)/', $father);
        if($matches->hasMatch()) {
            $attr['father_id'] = $matches->group(1);
        }

        $mother = explode(',', strip_tags($mother));
        if(count($mother) == 2) {
            [$attr['mother_surname'], $attr['mother_name']] = $mother;
        } else {
            $attr['mother_surname'] = implode(' ', $mother);
        }

        $father = explode(',', strip_tags($father));
        if(count($father) == 2) {
            [$attr['father_surname'], $attr['father_name']] = $father;
        } else {
            $attr['father_surname'] = implode(' ', $father);
        }

        return $attr;
    }

    private function parsePhoto(Crawler $crawler): array
    {
        try {
            return ['photo' => 'http://www.pytlewski.pl/index/drzewo/'.$crawler->filter('img')->attr('src')];
        } catch (InvalidArgumentException $e) {
            return [];
        }
    }

    private function parseRelations(Crawler $crawler): array
    {
        $attr = [
            'marriages' => [],
            'children' => [],
            'siblings' => [],
        ];

        $crawler = $crawler->children()->first()->children();

        $marriages = explode('</center>', $crawler->eq(0)->children()->first()->html())[1];
        $marriages = explode('<br>', $marriages);

        foreach ($marriages as $marriage) {
            $matches = Regex::match('/(?:<u><a href=".*id=([0-9]*)">)?([^<>(]+)(?:<\/a><\/u>)? ?(?:\(.*: ?([0-9.]*)(?:(?:,| )*([^)]*))?\))?/', $marriage);
            if ($matches->hasMatch() && ! Str::startsWith($matches->groupOr(2, ''), 'Nie zawar')) {
                $marriage = [];
                $marriage['id'] = $matches->groupOr(1, '');
                $marriage['name'] = $matches->groupOr(2, '');
                $marriage['date'] = $matches->groupOr(3, '');
                $marriage['place'] = $matches->groupOr(4, '');

                $attr['marriages'][] = $marriage;
            }
        }

        $children = explode('</center>', $crawler->eq(1)->children()->first()->html())[1];
        $children = explode('; ', $children);

        foreach ($children as $child) {
            $matches = Regex::match('/(?:<u><a href=".*id=([0-9]*)">)?([^<>]*)/', $child);
            if ($matches->hasMatch() && ! Str::startsWith($matches->groupOr(2, ''), 'Nie ma')) {
                $child = [];
                $child['id'] = $matches->groupOr(1, '');
                $child['name'] = $matches->groupOr(2, '');

                $attr['children'][] = $child;
            }
        }

        $siblings = explode('</center>', $crawler->eq(2)->children()->first()->html())[1];
        $siblings = explode('; ', $siblings);

        foreach ($siblings as $sibling) {
            $matches = Regex::match('/(?:<u><a href=".*id=([0-9]*)">)?([^<>]*)/', $sibling);
            if ($matches->hasMatch() && ! Str::startsWith($matches->groupOr(2, ''), 'Nie ma')) {
                $sibling = [];
                $sibling['id'] = $matches->groupOr(1, '');
                $sibling['name'] = $matches->groupOr(2, '');

                $attr['siblings'][] = $sibling;
            }
        }

        return $attr;
    }

    private function parseBio(Crawler $crawler): array
    {
        try {
            return ['bio' => $crawler->text() == 'pusto :(' ? null : $crawler->text()];
        } catch (InvalidArgumentException $e) {
            return [];
        }
    }
}
