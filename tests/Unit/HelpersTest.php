<?php

use Carbon\Carbon;

test('format date from period', function () {
    expect(format_date_from_period(new Carbon('1972-11-28'), new Carbon('1972-11-28')))
        ->toBe('1972-11-28');

    expect(format_date_from_period(new Carbon('2000-04-01'), new Carbon('2000-04-30')))
        ->toBe('2000-04');

    expect(format_date_from_period(new Carbon('2000-01-01'), new Carbon('2003-07-31')))
        ->toBe(__('misc.date.between').' 2000-01 '.__('misc.date.and').' 2003-07');

    expect(format_date_from_period(new Carbon('2011-01-01'), new Carbon('2011-02-28')))
        ->toBe(__('misc.date.between').' 2011-01 '.__('misc.date.and').' 2011-02');

    expect(format_date_from_period(new Carbon('1986-01-01'), new Carbon('1986-12-31')))
        ->toBe(1986);

    expect(format_date_from_period(new Carbon('1977-01-01'), new Carbon('1983-12-31')))
        ->toBe('1977-1983');

    expect(format_date_from_period(new Carbon('2002-12-17'), new Carbon('2015-10-31')))
        ->toBe(__('misc.date.between').' 2002-12-17 '.__('misc.date.and').' 2015-10-31');
});

test('roman helper', function () {
    expect(roman(1))->toBe('I');
    expect(roman(2))->toBe('II');
    expect(roman(3))->toBe('III');
    expect(roman(4))->toBe('IV');
    expect(roman(5))->toBe('V');
    expect(roman(6))->toBe('VI');
    expect(roman(7))->toBe('VII');
    expect(roman(8))->toBe('VIII');
    expect(roman(9))->toBe('IX');
    expect(roman(10))->toBe('X');
    expect(roman(14))->toBe('XIV');
    expect(roman(20))->toBe('XX');
    expect(roman(23))->toBe('XXIII');
    expect(roman(30))->toBe('XXX');
    expect(roman(39))->toBe('XXXIX');
});

test('faker helper returns faker', function () {
    $faker = faker();

    expect($faker)->toBeInstanceOf(\Faker\Generator::class);
});
