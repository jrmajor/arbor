<?php

namespace App\Services\Pytlewski\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use Spatie\Regex\Regex;

trait ScrapesPytlewski
{
    private function getParsers(): Collection
    {
        $reflection = new ReflectionClass(ScrapesPytlewski::class);

        return collect(
            $reflection->getMethods()
        )->map(
            fn ($method) => $method->name
        )->filter(
            fn ($method) => Str::startsWith($method, 'parse')
        );
    }

    private function parseBasicName($source): array
    {
        $name = substr(
            $source,
            strpos($source, '<font class="czcionka"><font size=3>') + 36
        );
        $name = substr($name, 0, strpos($name, '</font>'));
        $name = str_replace('<br>', ' ', $name);
        $name = str_replace('<i>', '', $name);
        $name = str_replace('</i>', '', $name);

        return ['basic_name' => $name];
    }

    private function parseBasicData($source): array
    {
        $matches = Regex::match('/<font class="czcionka"><font size=3><i><b>([^()<]*)(?: \()?([^()<]*)(?:\))?<\/b><br>([^<\s-]*)(?: |-)?([^<]*)?<\/i><\/font><br><font class="czcionka">ur\. ([^ ]*) w ([^<]*)(?:<br> \(zm\. )?([^ ]*)(?: w )?([^<),]*)(?:,poch\.)?([^<)]*)?\)?<\/font>/', $source);

        $attr = [];

        if ($matches->hasMatch()) {
            if (blank($matches->group(2))) {
                $attr['family_name'] = $matches->group(1);
            } else {
                $attr['last_name'] = $matches->group(1);
                $attr['family_name'] = $matches->group(2);
            }
            $attr['name'] = $matches->group(3);
            $attr['middle_name'] = str_replace('-', ' ', $matches->group(4));

            $attr['birth_date'] = $matches->group(5);

            if (! Str::contains($matches->group(6), 'brak')) {
                $attr['birth_place'] = $matches->group(6);
            }

            $attr['death_date'] = $matches->group(7);

            if (! Str::contains($matches->group(8), 'brak')) {
                $attr['death_place'] = $matches->group(8);
            }
            $attr['burial_place'] = $matches->group(9);
        }

        return $attr;
    }

    private function parseParents($source): array
    {
        $matches = Regex::match('/<b>Rodzice:<\/b><\/font><\/td><\/tr><tr><td colspan=2><font class="czcionka">(?:<a href="index\.php\?view=true&id=)?([0-9]*)(?:"><u>)?([^<,]*),? ?([^<]*)?(?:<\/u><\/a>)?<br>(?:<a href="index\.php\?view=true&id=)?([0-9]*)(?:"><u>)?([^<,]*),? ?([^<]*)?(?:<\/u><\/a>)?<\/font>/', $source);

        $attr = [];

        if ($matches->hasMatch()) {
            $attr['mother_id'] = $matches->group(1);
            $attr['mother_surname'] = $matches->group(2);
            $attr['mother_name'] = str_replace('-', ' ', $matches->group(3));
            $attr['father_id'] = $matches->group(4);
            $attr['father_surname'] = $matches->group(5);
            $attr['father_name'] = str_replace('-', ' ', $matches->group(6));
            $attr['parents'] = true;
        }

        return $attr;
    }

    private function parsePhoto($source): array
    {
        $matches = Regex::match('/tablica_06.jpg" width="125" height="165" align=center>(?:\S|\s)*(foto\/[0-9]*\.jpg|brak<br>zdj)/', $source);

        if ($matches->hasMatch() && $matches->group(1) != 'brak<br>zdj') {
            $photo = 'http://www.pytlewski.pl/index/drzewo/'.$matches->group(1);
        }

        return ['photo' => $photo ?? null];
    }

    private function parseFamily($source): array
    {
        $matches = Regex::match('/<b>Małżeństwa\(([0-9]+|)\):<\/b><\/center>(.*)<\/font>.*Dzieci\(([0-9]+|)\):<\/b><\/center>(.*)<\/font>.*Rodzeństwo\(([0-9]+|)\):<\/b><\/center>(.*)<\/font>/', $source);

        $attr = [];

        if ($matches->hasMatch()) {
            $attr['total_marriages'] = $matches->group(1);
            $attr['marriages'] = $this->marriagesHelper($matches->group(2));
            if ($attr['total_marriages'] == '' || $attr['total_marriages'] == 'brak') {
                $attr['total_marriages'] = '0';
            }

            $attr['total_children'] = $matches->group(3);
            $attr['children'] = $this->childrenHelper($matches->group(4));
            if ($attr['total_children'] == '' || $attr['total_children'] == 'brak') {
                $attr['total_children'] = '0';
            }

            $attr['total_siblings'] = $matches->group(5);
            $attr['siblings'] = $this->siblingsHelper($matches->group(6));
            if ($attr['total_siblings'] == '' || $attr['total_siblings'] == 'brak') {
                $attr['total_siblings'] = '0';
            }
        }

        return $attr;
    }

    private function marriagesHelper($data): array
    {
        $attr = explode('<br>', $data);

        foreach ($attr as $key => $val) {
            $matches = Regex::match('/(?:<u><a href="index\.php\?view=true&id=([0-9]*)">)?([^<>(]+)(?:<\/a><\/u>)? ?(?:\(.*: ?([0-9.]*)(?:(?:,| )*([^)]*))?\))?/', $val);
            if ($matches->hasMatch() && ! Str::startsWith($matches->groupOr(2, ''), 'Nie zawar')) {
                $attr[$key] = [];
                $attr[$key]['id'] = $matches->groupOr(1, '');
                $attr[$key]['name'] = $matches->groupOr(2, '');
                $attr[$key]['date'] = $matches->groupOr(3, '');
                $attr[$key]['place'] = $matches->groupOr(4, '');
            } else {
                unset($attr[$key]);
            }
        }

        return $attr;
    }

    private function childrenHelper($data): array
    {
        $attr = explode('; ', $data);

        foreach ($attr as $key => $val) {
            $matches = Regex::match('/(?:<u><a href="index\.php\?view=true&id=([0-9]*)">)?([^<>]+)(?:<\/a><\/u>)?/', $val);
            if ($matches->hasMatch() && ! Str::startsWith($matches->groupOr(2, ''), 'Nie ma')) {
                $attr[$key] = [];
                $attr[$key]['id'] = $matches->groupOr(1, '');
                $attr[$key]['name'] = $matches->groupOr(2, '');
            } else {
                unset($attr[$key]);
            }
        }

        return $attr;
    }

    private function siblingsHelper($data): array
    {
        $attr = explode('; ', $data);

        foreach ($attr as $key => $val) {
            $matches = Regex::match('/(?:<u><a href="index\.php\?view=true&id=([0-9]*)">)?([^<>]+)(?:<\/a><\/u>)?/', $val);
            if ($matches->hasMatch() && ! Str::startsWith($matches->groupOr(2, ''), 'Nie ma')) {
                $attr[$key] = [];
                $attr[$key]['id'] = $matches->groupOr(1, '');
                $attr[$key]['name'] = $matches->groupOr(2, '');
            } else {
                unset($attr[$key]);
            }
        }

        return $attr;
    }
}
