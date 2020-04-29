<?php

namespace App;

use Carbon\CarbonInterval;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Regex\Regex;

class Pytlewski
{
    private $id;
    private $url;
    private $source;
    private $attributes = [];

    public function __construct($id)
    {
        $this->id = $id;
        $this->prepareSource();
        $this->runParsers();
    }

    public static function url($id)
    {
        return 'http://www.pytlewski.pl/index/drzewo/index.php?view=true&id=' . $id;
    }

    private function prepareSource()
    {
        $this->setUrl();
        $this->getSource();
        $this->trimSource();
    }

    private function setUrl()
    {
        $this->url = self::url($this->id);
    }

    private function getSource()
    {
        $this->source = Cache::remember(
            "pytlewski.$this->id",
            CarbonInterval::day(),
            fn() => iconv('Windows-1250', 'UTF-8', file_get_contents($this->url))
        );
    }

    private function trimSource()
    {
        $this->source = $this->rightTrimSource($this->leftTrimSource($this->source));
    }

    private function leftTrimSource($source)
    {
        return strstr($source, '<table id="metrzyczka" width="481" height="451" border="0" cellpadding="0" cellspacing="0">');
    }

    private function rightTrimSource($source)
    {
        return strstr($source, '<td background="images/spacer.gif" width="35" height="1"></td>', true);
    }

    private function runParsers()
    {
        $this->parseBasicName();
        $this->parseBasicData();
        $this->parseParents();
        $this->parsePhoto();
        $this->parseFamily();

        $this->attributes = Arr::trim($this->attributes);
    }

    private function parseBasicName()
    {
        $this->basic_name = substr($this->source, strpos($this->source, '<font class="czcionka"><font size=3>') + 36);
        $this->basic_name = substr($this->basic_name, 0, strpos($this->basic_name, '</font>'));
        $this->basic_name =  str_replace('<br>', ' ', $this->basic_name);
        $this->basic_name =  str_replace('<i>', '', $this->basic_name);
        $this->basic_name =  str_replace('</i>', '', $this->basic_name);
    }

    private function parseBasicData()
    {
        $matches = Regex::match('/<font class="czcionka"><font size=3><i><b>([^()<]*)(?: \()?([^()<]*)(?:\))?<\/b><br>([^<\s-]*)(?: |-)?([^<]*)?<\/i><\/font><br><font class="czcionka">ur\. ([^ ]*) w ([^<]*)(?:<br> \(zm\. )?([^ ]*)(?: w )?([^<),]*)(?:,poch\.)?([^<)]*)?\)?<\/font>/', $this->source);
        if ($matches->hasMatch()) {
            if (blank($matches->group(2))) {
                $this->family_name = $matches->group(1);
            } else {
                $this->last_name = $matches->group(1);
                $this->family_name = $matches->group(2);
            }
            $this->name = $matches->group(3);
            $this->middle_name = str_replace('-', ' ', $matches->group(4));

            $birth = Regex::match('/([0-9]{1|2})?\.?([0-9]{1|2})?\.?([0-9]{3|4})/', $matches->group(5));
            if ($birth->hasMatch()) {
                $this->birth_date = $birth->group(3);
                if (filled($birth->group(2))) {
                    $this->birth_date .= '-' . str_pad($birth->group(2), 2, '0', STR_PAD_LEFT);
                }
                if (filled($birth->group(1))) {
                    $this->birth_date .= '-' . str_pad($birth->group(1), 2, '0', STR_PAD_LEFT);
                }
            }

            if (! Str::contains($matches->group(6), 'brak')) {
                $this->birth_place = $matches->group(6);
            }

            $death = Regex::match('/([0-9]{1|2})?\.?([0-9]{1|2})?\.?([0-9]{3|4})/', $matches->group(7));
            if ($death->hasMatch()) {
                $this->death_date = $death->group(3);
                if (filled($death->group(2))) {
                    $this->death_date .= '-' . str_pad($death->group(2), 2, '0', STR_PAD_LEFT);
                }
                if (filled($death->group(1))) {
                    $this->death_date .= '-' . str_pad($death->group(1), 2, '0', STR_PAD_LEFT);
                }
            }

            if (! Str::contains($matches->group(8), 'brak')) {
                $this->death_place = $matches->group(8);
            }
            $this->burial_place = $matches->group(9);
        }
    }

    private function parseParents()
    {
        $matches = Regex::match('/<b>Rodzice:<\/b><\/font><\/td><\/tr><tr><td colspan=2><font class="czcionka">(?:<a href="index\.php\?view=true&id=)?([0-9]*)(?:"><u>)?([^<,]*),? ?([^<]*)?(?:<\/u><\/a>)?<br>(?:<a href="index\.php\?view=true&id=)?([0-9]*)(?:"><u>)?([^<,]*),? ?([^<]*)?(?:<\/u><\/a>)?<\/font>/', $this->source);
        if ($matches->hasMatch()) {
            $this->mother_id = $matches->group(1);
            $this->mother_surname = $matches->group(2);
            $this->mother_name = str_replace('-', ' ', $matches->group(3));
            $this->father_id = $matches->group(4);
            $this->father_surname = $matches->group(5);
            $this->father_name = str_replace('-', ' ', $matches->group(6));
            $this->parents = true;
        }
    }

    private function parsePhoto()
    {
        $matches = Regex::match('/tablica_06.jpg" width="125" height="165" align=center>(?:\S|\s)*(foto\/[0-9]*\.jpg|brak<br>zdj)/', $this->source);
        if ($matches->hasMatch()) {
            $this->photo = 'http://www.pytlewski.pl/index/drzewo/' . $matches->group(1);
        }
    }

    private function parseFamily()
    {
        $matches = Regex::match('/<b>Małżeństwa\(([0-9]+|)\):<\/b><\/center>(.*)<\/font>.*Dzieci\(([0-9]+|)\):<\/b><\/center>(.*)<\/font>.*Rodzeństwo\(([0-9]+|)\):<\/b><\/center>(.*)<\/font>/', $this->source);
        if ($matches->hasMatch()) {
            $this->total_marriages = $matches->group(1);
            $this->marriages = $this->parseMarriages($matches->group(2));
            if ($this->total_marriages == '' || $this->total_marriages == 'brak'){
                $this->total_marriages = '0';
            }

            $this->total_children = $matches->group(3);
            $this->children = $this->parseChildren($matches->group(4));
            if ($this->total_children == '' || $this->total_children == 'brak'){
                $this->total_children = '0';
            }

            $this->total_siblings = $matches->group(5);
            $this->siblings = $this->parseSiblings($matches->group(6));
            if ($this->total_siblings == '' || $this->total_siblings == 'brak'){
                $this->total_siblings = '0';
            }
        }
    }

    private function parseMarriages($data)
    {
        $marriages_data = explode('<br>', $data);
        foreach($marriages_data as $key => $val) {
            $matches = Regex::match('/(?:<u><a href="index\.php\?view=true&id=([0-9]*)">)?([^<>(]+)(?:<\/a><\/u>)? ?(?:\(.*: ?([0-9.]*)(?:(?:,| )*([^)]*))?\))?/', $val);
            if($matches->hasMatch() && ! Str::startsWith($matches->groupOr(2, ''), 'Nie zawar')) {
                $marriages_data[$key] = [];
                $marriages_data[$key]['id'] = $matches->groupOr(1, '');
                $marriages_data[$key]['name'] = $matches->groupOr(2, '');
                $marriages_data[$key]['date'] = $matches->groupOr(3, '');
                $marriages_data[$key]['place'] = $matches->groupOr(4, '');
            } else {
                unset($marriages_data[$key]);
            }
        }
        return $marriages_data;
    }

    private function parseChildren($data)
    {
        $children_data = explode('; ', $data);
        foreach($children_data as $key => $val) {
            $matches = Regex::match('/(?:<u><a href="index\.php\?view=true&id=([0-9]*)">)?([^<>]+)(?:<\/a><\/u>)?/', $val);
            if($matches->hasMatch() && ! Str::startsWith($matches->groupOr(2, ''), 'Nie ma')) {
                $children_data[$key] = [];
                $children_data[$key]['id'] = $matches->groupOr(1, '');
                $children_data[$key]['name'] = $matches->groupOr(2, '');
            } else {
                unset($children_data[$key]);
            }
        }
            return $children_data;
    }

    private function parseSiblings($data)
    {
        $siblings_data = explode('; ', $data);
        foreach($siblings_data as $key => $val) {
            $matches = Regex::match('/(?:<u><a href="index\.php\?view=true&id=([0-9]*)">)?([^<>]+)(?:<\/a><\/u>)?/', $val);
            if($matches->hasMatch() && ! Str::startsWith($matches->groupOr(2, ''), 'Nie ma')) {
                $siblings_data[$key] = [];
                $siblings_data[$key]['id'] = $matches->groupOr(1, '');
                $siblings_data[$key]['name'] = $matches->groupOr(2, '');
            } else {
                unset($siblings_data[$key]);
            }
        }
            return $siblings_data;
    }

    public function __get($key)
    {
        switch ($key) {
            case 'id':
                return $this->id;
                break;
            case 'url':
                return $this->url;
                break;
            default:
                if(isset($this->attributes[$key])) {
                    return $this->attributes[$key];
                }
        }
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }
}
