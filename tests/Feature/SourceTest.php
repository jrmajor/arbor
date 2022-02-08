<?php

namespace Tests\Feature;

use App\Services\Sources\Source;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class SourceTest extends TestCase
{
    #[TestDox('it has raw() method')]
    public function testRaw(): void
    {
        $this->assertSame(
            '*test* [test](test) https://example.com',
            Source::from('*test* [test](test) https://example.com')->raw(),
        );
    }

    #[TestDox('it can sanitize input')]
    public function testSanitize(): void
    {
        $this->assertSame(
            '*text* [ *link* text](https://example.com)',
            Source::from("   *text*     \n    [  *link*\ntext](https://example.com)\r\n")->sanitized(),
        );
    }

    #[TestDox('it collapses whitespace')]
    public function testWhitespace(): void
    {
        $this->assertSame(
            '<i>text</i> <a href="https://example.com" class="a"><i>link</i> text</a>',
            Source::from("   *text*     \n    [  *link*\ntext](https://example.com)\r\n")->markup(),
        );
    }

    #[TestDox('it escapes html tags')]
    public function testHtmlTags(): void
    {
        $this->assertSame(
            e('<script>alert(1)</script><p style="display: none"><b>test</b>'),
            Source::from('<script>alert(1)</script><p style="display: none"><b>test</b>')->markup(),
        );
    }

    #[TestDox('it encodes html special characters')]
    public function testSpecialCharacters(): void
    {
        $this->assertSame(
            e('<>"&'),
            Source::from('<>"&')->markup(),
        );
    }

    #[TestDox('it parses links')]
    public function testLinksParse(): void
    {
        $this->assertSame(
            '<a href="https://wikipedia.com/wiki" class="a">wiki</a>',
            Source::from('[wiki](https://wikipedia.com/wiki)')->markup(),
        );
    }

    #[TestDox('it escapes links')]
    public function testLinkEscape(): void
    {
        $this->assertSame(
            '[wiki](h)',
            Source::from('\\[wiki](h)')->markup(),
        );
    }

    #[TestDox('it parses urls')]
    public function testUrls(): void
    {
        $this->assertSame(
            '<a href="https://wikipedia.com/wiki" class="a">https://wikipedia.com/wiki</a>',
            Source::from('https://wikipedia.com/wiki')->markup(),
        );
    }

    #[TestDox('it allows only http(s) links')]
    public function testOnlyHttpLinks(): void
    {
        $this->assertSame(
            '<a href="git%3A//wikipedia.com/wiki" class="a">git</a>',
            Source::from('[git](git://wikipedia.com/wiki)')->markup(),
        );
    }

    #[TestDox('it parses italics')]
    public function testItalicsParse(): void
    {
        $this->assertSame(
            '<i>text</i> <a href="https://weird.*domain*" class="a"><i>link</i> text</a>',
            Source::from('*text* [*link* text](https://weird.*domain*)')->markup(),
        );
    }

    #[TestDox('it escapes italics')]
    public function testItalicsEscape(): void
    {
        $this->assertSame(
            '*text* <a href="https://weird.*domain*" class="a">*link* text</a>',
            Source::from('\\*text\\* [\\*link* text](https://weird.*domain*)')->markup(),
        );
    }

    #[TestDox('it parses ISBN-10')]
    public function testIsbn10Parse(): void
    {
        $this->app->setLocale('pl');

        $this->assertSame(
            'exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/0306406158" target="_blank" title="ISBN 0-306-40615-8 w Wikipedii" class="a">ISBN 0-306-40615-8</a>',
            Source::from('exampIe ISBN 0-306-40615-8')->markup(),
        );

        $this->assertSame(
            'exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/030640615X" target="_blank" title="ISBN 0-306-40615-X w Wikipedii" class="a">ISBN 0-306-40615-X</a> text',
            Source::from('exampIe ISBN 0-306-40615-X text')->markup(),
        );
    }

    #[TestDox('it localizes ISBN-10')]
    public function testIsbn10Localize(): void
    {
        app()->setLocale('pl');

        $this->assertSame(
            'exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/0306406158" target="_blank" title="ISBN 0-306-40615-8 w Wikipedii" class="a">ISBN 0-306-40615-8</a>',
            Source::from('exampIe ISBN 0-306-40615-8')->markup(),
        );

        app()->setLocale('en');

        $this->assertSame(
            'exampIe <a href="https://en.wikipedia.org/wiki/Special:BookSources/0306406158" target="_blank" title="ISBN 0-306-40615-8 in Wikipedia" class="a">ISBN 0-306-40615-8</a>',
            Source::from('exampIe ISBN 0-306-40615-8')->markup(),
        );
    }

    #[TestDox('it parses ISBN-13')]
    public function testIsbn13Parse(): void
    {
        app()->setLocale('pl');

        $this->assertSame(
            'exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/9780306406157" target="_blank" title="ISBN 978-0-306-40615-7 w Wikipedii" class="a">ISBN 978-0-306-40615-7</a> text',
            Source::from('exampIe ISBN 978-0-306-40615-7 text')->markup(),
        );

        $this->assertSame(
            'exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/9790306406157" target="_blank" title="ISBN 979-0-306-40615-7 w Wikipedii" class="a">ISBN 979-0-306-40615-7</a> text',
            Source::from('exampIe ISBN 979-0-306-40615-7 text')->markup(),
        );

        $this->assertSame(
            'exampIe ISBN 999-0-306-40615-7 text',
            Source::from('exampIe ISBN 999-0-306-40615-7 text')->markup(),
        );
    }

    #[TestDox('it localizes ISBN-13')]
    public function testIsbn13Localize(): void
    {
        app()->setLocale('pl');

        $this->assertSame(
            'exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/9780306406157" target="_blank" title="ISBN 978-0-306-40615-7 w Wikipedii" class="a">ISBN 978-0-306-40615-7</a> text',
            Source::from('exampIe ISBN 978-0-306-40615-7 text')->markup(),
        );

        app()->setLocale('en');

        $this->assertSame(
            'exampIe <a href="https://en.wikipedia.org/wiki/Special:BookSources/9780306406157" target="_blank" title="ISBN 978-0-306-40615-7 in Wikipedia" class="a">ISBN 978-0-306-40615-7</a> text',
            Source::from('exampIe ISBN 978-0-306-40615-7 text')->markup(),
        );
    }
}
