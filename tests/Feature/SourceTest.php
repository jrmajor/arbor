<?php

use App\Source;

it('has raw method')
    ->assertEquals(
        '*test* [test](test) https://example.com',
        Source::from('*test* [test](test) https://example.com')->raw()
    );

it('can sanitize input')
    ->assertEquals(
        '*text* [ *link* text](https://example.com)',
        Source::from("   *text*     \n    [  *link*\ntext](https://example.com)\r\n")->sanitized()
    );

it('collapses whitespace')
    ->assertEquals(
        '<i>text</i> <a href="https://example.com" class="a"><i>link</i> text</a>',
        Source::from("   *text*     \n    [  *link*\ntext](https://example.com)\r\n")->markup()
    );

it('encodes html tags')
    ->assertEquals(
        e('<script>alert(1)</script><p style="display: none"><b>test</b>'),
        Source::from('<script>alert(1)</script><p style="display: none"><b>test</b>')->markup()
    );

it('encodes html special characters')
    ->assertEquals(
        e('<>"&'),
        Source::from('<>"&')->markup()
    );

it('parses links')
    ->assertEquals(
        '<a href="https://wikipedia.com/wiki" class="a">wiki</a>',
        Source::from('[wiki](https://wikipedia.com/wiki)')->markup()
    );

it('escapes links')
    ->assertEquals(
        '[wiki](h)',
        Source::from('\[wiki](h)')->markup()
    );

it('parses urls')
    ->assertEquals(
        '<a href="https://wikipedia.com/wiki" class="a">https://wikipedia.com/wiki</a>',
        Source::from('https://wikipedia.com/wiki')->markup()
    );

it('allows only http(s) links')
    ->assertEquals(
        '<a href="git%3A//wikipedia.com/wiki" class="a">git</a>',
        Source::from('[git](git://wikipedia.com/wiki)')->markup()
    );

it('parses italics')
    ->assertEquals(
        '<i>text</i> <a href="https://weird.*domain*" class="a"><i>link</i> text</a>',
        Source::from('*text* [*link* text](https://weird.*domain*)')->markup()
    );

it('escapes italics')
    ->assertEquals(
        '*text* <a href="https://weird.*domain*" class="a">*link* text</a>',
        Source::from('\*text\* [\*link* text](https://weird.*domain*)')->markup()
    );

it('parses ISBN-10', function() {
    assertEquals(
        'exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/0306406158" target="_blank" title="ISBN 0-306-40615-8 w Wikipedii" class="a">ISBN 0-306-40615-8</a>',
        Source::from('exampIe ISBN 0-306-40615-8')->markup()
    );

    assertEquals(
        'exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/030640615X" target="_blank" title="ISBN 0-306-40615-X w Wikipedii" class="a">ISBN 0-306-40615-X</a> text',
        Source::from('exampIe ISBN 0-306-40615-X text')->markup()
    );
})->only();

it('parses ISBN-13', function() {
    assertEquals(
        'exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/9780306406157" target="_blank" title="ISBN 978-0-306-40615-7 w Wikipedii" class="a">ISBN 978-0-306-40615-7</a> text',
        Source::from('exampIe ISBN 978-0-306-40615-7 text')->markup()
    );

    assertEquals(
        'exampIe <a href="https://pl.wikipedia.org/wiki/Specjalna:Książki/9790306406157" target="_blank" title="ISBN 979-0-306-40615-7 w Wikipedii" class="a">ISBN 979-0-306-40615-7</a> text',
        Source::from('exampIe ISBN 979-0-306-40615-7 text')->markup()
    );

    assertEquals(
        'exampIe ISBN 999-0-306-40615-7 text',
        Source::from('exampIe ISBN 999-0-306-40615-7 text')->markup()
    );
})->only();
