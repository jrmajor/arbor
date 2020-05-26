<?php

use Carbon\Carbon;

test('format date from period', function () {
    assertEquals(
        '1972-11-28',
        format_date_from_period(new Carbon('1972-11-28'), new Carbon('1972-11-28'))
    );

    assertEquals(
        '2000-04',
        format_date_from_period(new Carbon('2000-04-01'), new Carbon('2000-04-30'))
    );

    assertEquals(
        __('misc.date.between').' 2000-01 '.__('misc.date.and').' 2003-07',
        format_date_from_period(new Carbon('2000-01-01'), new Carbon('2003-07-31'))
    );

    assertEquals(
        __('misc.date.between').' 2011-01 '.__('misc.date.and').' 2011-02',
        format_date_from_period(new Carbon('2011-01-01'), new Carbon('2011-02-28'))
    );

    assertEquals(
        '1986',
        format_date_from_period(new Carbon('1986-01-01'), new Carbon('1986-12-31'))
    );

    assertEquals(
        '1977-1983',
        format_date_from_period(new Carbon('1977-01-01'), new Carbon('1983-12-31'))
    );

    assertEquals(
        __('misc.date.between').' 2002-12-17 '.__('misc.date.and').' 2015-10-31',
        format_date_from_period(new Carbon('2002-12-17'), new Carbon('2015-10-31'))
    );
});

test('roman helper', function () {
    assertEquals('I', roman(1));
    assertEquals('II', roman(2));
    assertEquals('III', roman(3));
    assertEquals('IV', roman(4));
    assertEquals('V', roman(5));
    assertEquals('VI', roman(6));
    assertEquals('VII', roman(7));
    assertEquals('VIII', roman(8));
    assertEquals('IX', roman(9));
    assertEquals('X', roman(10));
    assertEquals('XIV', roman(14));
    assertEquals('XX', roman(20));
    assertEquals('XXIII', roman(23));
    assertEquals('XXX', roman(30));
    assertEquals('XXXIX', roman(39));
});

test('faker helper returns faker', function () {
    $faker = faker();

    assertTrue($faker instanceof \Faker\Generator);
});
