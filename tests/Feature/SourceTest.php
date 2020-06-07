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
        '<i>text</i> <a href="https://example.com"><i>link</i> text</a>',
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
        '<a href="https://wikipedia.com/wiki">wiki</a>',
        Source::from('[wiki](https://wikipedia.com/wiki)')->markup()
    );

it('escapes links')
    ->assertEquals(
        '[wiki](h)',
        Source::from('\[wiki](h)')->markup()
    );

it('parses urls')
    ->assertEquals(
        '<a href="https://wikipedia.com/wiki">https://wikipedia.com/wiki</a>',
        Source::from('https://wikipedia.com/wiki')->markup()
    );

it('allows only http(s) links')
    ->assertEquals(
        '<a href="git%3A//wikipedia.com/wiki">git</a>',
        Source::from('[git](git://wikipedia.com/wiki)')->markup()
    );

it('parses italics')
    ->assertEquals(
        '<i>text</i> <a href="https://weird.*domain*"><i>link</i> text</a>',
        Source::from('*text* [*link* text](https://weird.*domain*)')->markup()
    );

it('escapes italics')
    ->assertEquals(
        '*text* <a href="https://weird.*domain*">*link* text</a>',
        Source::from('\*text\* [\*link* text](https://weird.*domain*)')->markup()
    );
