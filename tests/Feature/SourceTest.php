<?php

use App\Services\Sources\Source;

it('has raw method')
    ->expect(Source::from('*test* [test](test) https://example.com')->raw())
    ->toBe('*test* [test](test) https://example.com');

it('can sanitize input')
    ->expect(Source::from("   *text*     \n    [  *link*\ntext](https://example.com)\r\n")->sanitized())
    ->toBe('*text* [ *link* text](https://example.com)');

it('collapses whitespace')
    ->expect(Source::from("   *text*     \n    [  *link*\ntext](https://example.com)\r\n")->markup())
    ->toBe('<i>text</i> <a href="https://example.com" class="a"><i>link</i> text</a>');

it('encodes html tags')
    ->expect(Source::from('<script>alert(1)</script><p style="display: none"><b>test</b>')->markup())
    ->toBe(e('<script>alert(1)</script><p style="display: none"><b>test</b>'));

it('encodes html special characters')
    ->expect(Source::from('<>"&')->markup())
    ->toBe(e('<>"&'));

it('parses links')
    ->expect(Source::from('[wiki](https://wikipedia.com/wiki)')->markup())
    ->toBe('<a href="https://wikipedia.com/wiki" class="a">wiki</a>');

it('escapes links')
    ->expect(Source::from('\\[wiki](h)')->markup())
    ->toBe('[wiki](h)');

it('parses urls')
    ->expect(Source::from('https://wikipedia.com/wiki')->markup())
    ->toBe('<a href="https://wikipedia.com/wiki" class="a">https://wikipedia.com/wiki</a>');

it('allows only http(s) links')
    ->expect(Source::from('[git](git://wikipedia.com/wiki)')->markup())
    ->toBe('<a href="git%3A//wikipedia.com/wiki" class="a">git</a>');

it('parses italics')
    ->expect(Source::from('*text* [*link* text](https://weird.*domain*)')->markup())
    ->toBe('<i>text</i> <a href="https://weird.*domain*" class="a"><i>link</i> text</a>');

it('escapes italics')
    ->expect(Source::from('\\*text\\* [\\*link* text](https://weird.*domain*)')->markup())
    ->toBe('*text* <a href="https://weird.*domain*" class="a">*link* text</a>');

it('parses ISBN-10', function () {
    app()->setLocale('pl');

    expect(Source::from('exampIe ISBN 0-306-40615-8')->markup())
        ->toBe('exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/0306406158" target="_blank" title="ISBN 0-306-40615-8 w Wikipedii" class="a">ISBN 0-306-40615-8</a>');

    expect(Source::from('exampIe ISBN 0-306-40615-X text')->markup())
        ->toBe('exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/030640615X" target="_blank" title="ISBN 0-306-40615-X w Wikipedii" class="a">ISBN 0-306-40615-X</a> text');
});

it('localizes ISBN-10', function () {
    app()->setLocale('pl');

    expect(Source::from('exampIe ISBN 0-306-40615-8')->markup())
        ->toBe('exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/0306406158" target="_blank" title="ISBN 0-306-40615-8 w Wikipedii" class="a">ISBN 0-306-40615-8</a>');

    app()->setLocale('en');

    expect(Source::from('exampIe ISBN 0-306-40615-8')->markup())
        ->toBe('exampIe <a href="https://en.wikipedia.org/wiki/Special:BookSources/0306406158" target="_blank" title="ISBN 0-306-40615-8 in Wikipedia" class="a">ISBN 0-306-40615-8</a>');
});

it('parses ISBN-13', function () {
    app()->setLocale('pl');

    expect(Source::from('exampIe ISBN 978-0-306-40615-7 text')->markup())
        ->toBe('exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/9780306406157" target="_blank" title="ISBN 978-0-306-40615-7 w Wikipedii" class="a">ISBN 978-0-306-40615-7</a> text');

    expect(Source::from('exampIe ISBN 979-0-306-40615-7 text')->markup())
        ->toBe('exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/9790306406157" target="_blank" title="ISBN 979-0-306-40615-7 w Wikipedii" class="a">ISBN 979-0-306-40615-7</a> text');

    expect(Source::from('exampIe ISBN 999-0-306-40615-7 text')->markup())
        ->toBe('exampIe ISBN 999-0-306-40615-7 text');
});

it('localizes ISBN-13', function () {
    app()->setLocale('pl');

    expect(Source::from('exampIe ISBN 978-0-306-40615-7 text')->markup())
        ->toBe('exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/9780306406157" target="_blank" title="ISBN 978-0-306-40615-7 w Wikipedii" class="a">ISBN 978-0-306-40615-7</a> text');

    app()->setLocale('en');

    expect(Source::from('exampIe ISBN 978-0-306-40615-7 text')->markup())
        ->toBe('exampIe <a href="https://en.wikipedia.org/wiki/Special:BookSources/9780306406157" target="_blank" title="ISBN 978-0-306-40615-7 in Wikipedia" class="a">ISBN 978-0-306-40615-7</a> text');
});
