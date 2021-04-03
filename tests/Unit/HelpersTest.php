<?php

use Carbon\Carbon;

test('format date from period', function () {
    expect(
        (new Carbon('1972-11-28'))->formatPeriodTo(new Carbon('1972-11-28'))
    )->toBe('1972-11-28');

    expect(
        (new Carbon('2000-04-01'))->formatPeriodTo(new Carbon('2000-04-30'))
    )->toBe('2000-04');

    expect(
        (new Carbon('2000-01-01'))->formatPeriodTo(new Carbon('2003-07-31'))
    )->toBe('between 2000-01 and 2003-07');

    expect(
        (new Carbon('2011-01-01'))->formatPeriodTo(new Carbon('2011-02-28'))
    )->toBe('between 2011-01 and 2011-02');

    expect(
        (new Carbon('1986-01-01'))->formatPeriodTo(new Carbon('1986-12-31'))
    )->toBe('1986');

    expect(
        (new Carbon('1977-01-01'))->formatPeriodTo(new Carbon('1983-12-31'))
    )->toBe('1977-1983');

    expect(
        (new Carbon('2002-12-17'))->formatPeriodTo(new Carbon('2015-10-31'))
    )->toBe('between 2002-12-17 and 2015-10-31');
});

test('roman helper')
    ->expect(roman(1))->toBe('I')
    ->and(roman(2))->toBe('II')
    ->and(roman(3))->toBe('III')
    ->and(roman(4))->toBe('IV')
    ->and(roman(5))->toBe('V')
    ->and(roman(6))->toBe('VI')
    ->and(roman(7))->toBe('VII')
    ->and(roman(8))->toBe('VIII')
    ->and(roman(9))->toBe('IX')
    ->and(roman(10))->toBe('X')
    ->and(roman(14))->toBe('XIV')
    ->and(roman(20))->toBe('XX')
    ->and(roman(23))->toBe('XXIII')
    ->and(roman(30))->toBe('XXX')
    ->and(roman(39))->toBe('XXXIX');

test('faker helper returns faker')
    ->expect(faker())
    ->toBeInstanceOf(\Faker\Generator::class);
