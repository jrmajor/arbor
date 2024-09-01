<?php

namespace App\Services\Sources;

use Illuminate\Contracts\Support\Jsonable;
use Psl\Regex;
use Psl\Str;

/**
 * @phpstan-type InlineType = 'EscapeSequence'|'Italics'|'ISBN'|'Link'|'SpecialCharacter'|'Url'
 * @phpstan-type Handler = 'line'
 * @phpstan-type Excerpt = array{text: string, context: string}
 * @phpstan-type Element = array{
 *     name: string,
 *     handler?: Handler,
 *     nonNestables?: list<string>,
 *     text: ?string,
 *     attributes?: array<string, ?string>,
 * }
 */
final class Source implements Jsonable
{
    public const string ParsedownVersion = '1.7.4';

    /** @var array<string, list<InlineType>> */
    private array $inlineTypes = [
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

    private string $inlineMarkerList = '"&<>:[*I\\';

    public function __construct(
        private ?string $raw,
    ) { }

    public static function from(string|self|null $raw): self
    {
        return $raw instanceof self ? $raw : new self($raw);
    }

    public function markup(): string
    {
        return $this->line($this->raw);
    }

    public function sanitized(): ?string
    {
        $collapsed = trim(preg_replace('/\\s+/', ' ', $this->raw));

        return $collapsed === '' ? null : $collapsed;
    }

    public function raw(): ?string
    {
        return $this->raw;
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->raw, $options);
    }

    /**
     * @param list<string> $nonNestables
     */
    private function line(string $text, array $nonNestables = []): string
    {
        $markup = '';

        // $excerpt is based on the first occurrence of a marker
        while ($excerpt = strpbrk($text, $this->inlineMarkerList)) {
            $marker = $excerpt[0];

            $markerPosition = strpos($text, $marker);

            $excerpt = ['text' => $excerpt, 'context' => $text];

            foreach ($this->inlineTypes[$marker] as $inlineType) {
                // check to see if the current inline type is nestable in the current context
                if ($nonNestables && in_array($inlineType, $nonNestables)) {
                    continue;
                }

                $inline = match ($inlineType) {
                    'EscapeSequence' => $this->inlineEscapeSequence($excerpt),
                    'Italics' => $this->inlineItalics($excerpt),
                    'ISBN' => $this->inlineISBN($excerpt),
                    'Link' => $this->inlineLink($excerpt),
                    'SpecialCharacter' => $this->inlineSpecialCharacter($excerpt),
                    'Url' => $this->inlineUrl($excerpt),
                };

                if ($inline === null) {
                    continue;
                }

                // makes sure that the inline belongs to "our" marker
                if (isset($inline['position']) && $inline['position'] > $markerPosition) {
                    continue;
                }

                // sets a default inline position
                $inline['position'] ??= $markerPosition;

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

        return trim(preg_replace('/\\s+/', ' ', $markup));
    }

    /**
     * @param Excerpt $excerpt
     *
     * @return ?array{extent: int, markup: string}
     */
    private function inlineEscapeSequence(array $excerpt): ?array
    {
        if (! in_array($excerpt['text'][1] ?? null, ['\\', '*', '[', ']', '(', ')'])) {
            return null;
        }

        return [
            'markup' => $excerpt['text'][1],
            'extent' => 2,
        ];
    }

    /**
     * @param Excerpt $excerpt
     *
     * @return ?array{extent: int, element: Element}
     */
    private function inlineItalics(array $excerpt): ?array
    {
        if (! isset($excerpt['text'][1])) {
            return null;
        }

        if (! preg_match('/^[*]((?:\\\\\\*|[^*]|[*][*][^*]+?[*][*])+?)[*](?![*])/', $excerpt['text'], $matches)) {
            return null;
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

    /**
     * @param Excerpt $excerpt
     *
     * @return ?array{extent: int, element: Element}
     */
    private function inlineISBN(array $excerpt): ?array
    {
        if (! isset($excerpt['text'][1])) {
            return null;
        }

        if (! preg_match('/^ISBN ((?:978|979)?[- ]?(?:\\d[- ]?){9}[\\dXx])(\\s|$)/', $excerpt['text'], $matches)) {
            return null;
        }

        $number = Str\replace_every($matches[1], ['-' => '', ' ' => '']);

        return [
            // @phpstan-ignore identical.alwaysFalse
            'extent' => $matches[2] === '' ? strlen($matches[0]) : strlen($matches[0]) - 1,
            'element' => [
                'name' => 'a',
                'text' => 'ISBN ' . $matches[1],
                'attributes' => [
                    'href' => __('people.isbn_url') . $number,
                    'target' => '_blank',
                    'title' => 'ISBN ' . $matches[1] . ' ' . __('people.isbn_in_wikipedia'),
                ],
            ],
        ];
    }

    /**
     * @param Excerpt $excerpt
     *
     * @return ?array{extent: int, element: Element}
     */
    private function inlineLink(array $excerpt): ?array
    {
        $element = [
            'name' => 'a',
            'handler' => 'line',
            'nonNestables' => ['Url', 'Link'],
            'text' => null,
            'attributes' => [
                'href' => null,
                'title' => null,
            ],
        ];

        $extent = 0;

        $remainder = $excerpt['text'];

        if (! preg_match('/\\[((?:[^][]++|(?R))*+)\\]/', $remainder, $matches)) {
            return null;
        }

        $element['text'] = $matches[1];

        $extent += strlen($matches[0]);

        $remainder = substr($remainder, $extent);

        if (! preg_match('/^[(]\\s*+((?:[^ ()]++|[(][^ )]+[)])++)\\s*[)]/', $remainder, $matches)) {
            return null;
        }

        $element['attributes']['href'] = $matches[1];

        $extent += strlen($matches[0]);

        return [
            'extent' => $extent,
            'element' => $element,
        ];
    }

    /**
     * @param Excerpt $excerpt
     *
     * @return array{extent: int, markup: string}
     */
    private function inlineSpecialCharacter(array $excerpt): array
    {
        if (preg_match('/^&#?\\w+;/', $excerpt['text'])) {
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

    /**
     * @param Excerpt $excerpt
     *
     * @return ?array{extent: int, position: int, element: Element}
     */
    private function inlineUrl(array $excerpt): ?array
    {
        if (! isset($excerpt['text'][2]) || $excerpt['text'][2] !== '/') {
            return null;
        }

        if (! preg_match('/\\bhttps?:[\\/]{2}[^\\s<]+\\b\\/*/ui', $excerpt['context'], $matches, PREG_OFFSET_CAPTURE)) {
            return null;
        }

        $url = $matches[0][0];

        return [
            'extent' => strlen($matches[0][0]),
            'position' => $matches[0][1],
            'element' => [
                'name' => 'a',
                'text' => $url,
                'attributes' => [
                    'href' => $url,
                ],
            ],
        ];
    }

    /**
     * @param Element $element
     */
    private function element(array $element): string
    {
        $element = $this->sanitiseElement($element);

        $markup = '<' . $element['name'];

        foreach ($element['attributes'] ?? [] as $name => $value) {
            if ($value === null) {
                continue;
            }

            $markup .= ' ' . $name . '="' . e($value) . '"';
        }

        $text = $element['text'] ?? null;

        if ($text === null) {
            return $markup . '/>';
        }

        $markup .= '>';

        $markup .= match ($element['handler'] ?? null) {
            'line' => $this->line($text, $element['nonNestables'] ?? []),
            null => e($text),
        };

        return $markup . '</' . $element['name'] . '>';
    }

    /**
     * @param Element $element
     *
     * @return Element
     */
    private function sanitiseElement(array $element): array
    {
        if ($element['name'] !== 'a') {
            return $element;
        }

        if (! Regex\matches($element['attributes']['href'], '#^https?://#')) {
            $element['attributes']['href'] = str_replace(':', '%3A', $element['attributes']['href']);
        }

        return $element;
    }

    public function __toString(): string
    {
        return (string) $this->raw;
    }
}
