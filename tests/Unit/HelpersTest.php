<?php

use Carbon\Carbon;

test('carbon() helper')
    ->expect(carbon('2021-04-01'))
    ->toBeInstanceOf(Carbon::class);

test('format date from period carbon macro', function () {
    expect(carbon('1972-11-28')->formatPeriodTo(carbon('1972-11-28')))
        ->toBe('1972-11-28');

    expect(carbon('2000-04-01')->formatPeriodTo(carbon('2000-04-30')))
        ->toBe('2000-04');

    expect(carbon('2000-01-01')->formatPeriodTo(carbon('2003-07-31')))
        ->toBe('between 2000-01 and 2003-07');

    expect(carbon('2011-01-01')->formatPeriodTo(carbon('2011-02-28')))
        ->toBe('between 2011-01 and 2011-02');

    expect(carbon('1986-01-01')->formatPeriodTo(carbon('1986-12-31')))
        ->toBe('1986');

    expect(carbon('1977-01-01')->formatPeriodTo(carbon('1983-12-31')))
        ->toBe('1977-1983');

    expect(carbon('2002-12-17')->formatPeriodTo(carbon('2015-10-31')))
        ->toBe('between 2002-12-17 and 2015-10-31');
});

test('roman() helper')
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

test('faker() helper')
    ->expect(faker())
    ->toBeInstanceOf(\Faker\Generator::class);
