<?php

namespace App;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;

class Source implements Jsonable
{
    const parsedownVersion = '1.7.4';

    protected $raw;

    public function __construct($raw)
    {
        $this->raw = $raw;
    }

    public static function from($raw)
    {
        return $raw instanceof self ? $raw : new self($raw);
    }

    public function markup()
    {
        return $this->line($this->raw);
    }

    public function sanitized()
    {
        $collapsed = trim(preg_replace('/\s+/', ' ', $this->raw));

        return $collapsed === '' ? null : $collapsed;
    }

    public function raw()
    {
        return $this->raw;
    }

    public function __toString()
    {
        return (string) $this->raw;
    }

    public function toJson($options = 0)
    {
        return json_encode($this->raw, $options);
    }

    protected $inlineTypes = [
        '"' => ['SpecialCharacter'],
        '&' => ['SpecialCharacter'],
        '<' => ['SpecialCharacter'],
        '>' => ['SpecialCharacter'],
        ':' => ['Url'],
        '[' => ['Link'],
        '*' => ['Italics'],
        'I' => ['ISBN'],
        '\\' => ['EscapeSequence'],
    ];

    protected $inlineMarkerList = '"&<>:[*I\\';

    protected function line($text, $nonNestables = [])
    {
        $markup = '';

        // $excerpt is based on the first occurrence of a marker
        while ($excerpt = strpbrk($text, $this->inlineMarkerList)) {
            $marker = $excerpt[0];

            $markerPosition = strpos($text, $marker);

            $excerpt = ['text' => $excerpt, 'context' => $text];

            foreach ($this->inlineTypes[$marker] as $inlineType) {
                // check to see if the current inline type is nestable in the current context
                if (! empty($nonNestables) && in_array($inlineType, $nonNestables)) {
                    continue;
                }

                $inline = $this->{'inline'.$inlineType}($excerpt);

                if (! isset($inline)) {
                    continue;
                }

                // makes sure that the inline belongs to "our" marker
                if (isset($inline['position']) && $inline['position'] > $markerPosition) {
                    continue;
                }

                // sets a default inline position
                if (! isset($inline['position'])) {
                    $inline['position'] = $markerPosition;
                }

                // cause the new element to 'inherit' our non nestables
                foreach ($nonNestables as $nonNestable) {
                    $inline['element']['nonNestables'][] = $nonNestable;
                }

                // the text that comes before the inline
                $markup .= substr($text, 0, $inline['position']);

                // compile the inline
                $markup .= $inline['markup'] ?? $this->element($inline['element']);

                // remove the examined text
                $text = substr($text, $inline['position'] + $inline['extent']);

                continue 2;
            }

            // the marker does not belong to an inline
            $markup .= substr($text, 0, $markerPosition + 1);

            $text = substr($text, $markerPosition + 1);
        }

        $markup .= $text;

        return trim(preg_replace('/\s+/', ' ', $markup));
    }

    protected function inlineEscapeSequence($excerpt)
    {
        if (isset($excerpt['text'][1]) && in_array($excerpt['text'][1], ['\\', '*', '[', ']', '(', ')'])) {
            return [
                'markup' => $excerpt['text'][1],
                'extent' => 2,
            ];
        }
    }

    protected function inlineItalics($excerpt)
    {
        if (! isset($excerpt['text'][1])) {
            return;
        }

        if (! preg_match('/^[*]((?:\\\\\*|[^*]|[*][*][^*]+?[*][*])+?)[*](?![*])/s', $excerpt['text'], $matches)) {
            return;
        }

        return [
            'extent' => strlen($matches[0]),
            'element' => [
                'name' => 'i',
                'handler' => 'line',
                'text' => $matches[1],
            ],
        ];
    }

    protected function inlineISBN($excerpt)
    {
        if (! isset($excerpt['text'][1])) {
            return;
        }

        if (! preg_match('/^ISBN ((?:978|979)?[- ]?(?:\d[- ]?){9}[\dXx])(\s|$)/s', $excerpt['text'], $matches)) {
            return;
        }

        $number = Str::of($matches[1])
            ->replace('-', '')
            ->replace(' ', '');

        return [
            'extent' => $matches[2] === '' ? strlen($matches[0]) : strlen($matches[0]) - 1,
            'element' => [
                'name' => 'a',
                'text' => 'ISBN '.$matches[1],
                'attributes' => [
                    'href' => __('people.isbn_url').$number,
                    'target' => '_blank',
                    'title' => 'ISBN '.$matches[1].' '.__('people.isbn_in_wikipedia'),
                    'class' => 'a',
                ],
            ],
        ];
    }

    protected function inlineLink($excerpt)
    {
        $element = [
            'name' => 'a',
            'handler' => 'line',
            'nonNestables' => ['Url', 'Link'],
            'text' => null,
            'attributes' => [
                'href' => null,
                'title' => null,
                'class' => 'a',
            ],
        ];

        $extent = 0;

        $remainder = $excerpt['text'];

        if (preg_match('/\[((?:[^][]++|(?R))*+)\]/', $remainder, $matches)) {
            $element['text'] = $matches[1];

            $extent += strlen($matches[0]);

            $remainder = substr($remainder, $extent);
        } else {
            return;
        }

        if (preg_match('/^[(]\s*+((?:[^ ()]++|[(][^ )]+[)])++)\s*[)]/', $remainder, $matches)) {
            $element['attributes']['href'] = $matches[1];

            $extent += strlen($matches[0]);
        } else {
            return;
        }

        return [
            'extent' => $extent,
            'element' => $element,
        ];
    }

    protected function inlineSpecialCharacter($excerpt)
    {
        if (preg_match('/^&#?\w+;/', $excerpt['text'])) {
            return [
                'markup' => '&',
                'extent' => 1,
            ];
        }

        return [
            'markup' => e($excerpt['text'][0]),
            'extent' => 1,
        ];
    }

    protected function inlineUrl($excerpt)
    {
        if (! isset($excerpt['text'][2]) || $excerpt['text'][2] !== '/') {
            return;
        }

        if (preg_match('/\bhttps?:[\/]{2}[^\s<]+\b\/*/ui', $excerpt['context'], $matches, PREG_OFFSET_CAPTURE)) {
            $url = $matches[0][0];

            $inline = [
                'extent' => strlen($matches[0][0]),
                'position' => $matches[0][1],
                'element' => [
                    'name' => 'a',
                    'text' => $url,
                    'attributes' => [
                        'href' => $url,
                        'class' => 'a',
                    ],
                ],
            ];

            return $inline;
        }
    }

    protected function element(array $element)
    {
        $element = $this->sanitiseElement($element);

        $markup = '<'.$element['name'];

        if (isset($element['attributes'])) {
            foreach ($element['attributes'] as $name => $value) {
                if ($value === null) {
                    continue;
                }

                $markup .= ' '.$name.'="'.e($value).'"';
            }
        }

        if (isset($element['text'])) {
            $text = $element['text'];
        }

        if (isset($text)) {
            $markup .= '>';

            if (! isset($element['nonNestables'])) {
                $element['nonNestables'] = [];
            }

            if (isset($element['handler'])) {
                $markup .= $this->{$element['handler']}($text, $element['nonNestables']);
            } else {
                $markup .= e($text, true);
            }

            $markup .= '</'.$element['name'].'>';
        } else {
            $markup .= '/>';
        }

        return $markup;
    }

    protected function sanitiseElement(array $element)
    {
        if ($element['name'] !== 'a') {
            return $element;
        }

        if (! Str::startsWith(strtolower($element['attributes']['href']), ['http://', 'https://'])) {
            $element['attributes']['href'] = str_replace(':', '%3A', $element['attributes']['href']);
        }

        return $element;
    }
}
