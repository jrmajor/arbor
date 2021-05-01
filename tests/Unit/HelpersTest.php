<?php

test('carbon() helper')
    ->expect(carbon('2021-04-01'))
    ->toBeInstanceOf(Carbon\Carbon::class)
    ->and(carbon('2005-04-02 21:37')->format(DateTime::ATOM))
    ->toBe('2005-04-02T21:37:00+00:00');

test('format date from period carbon macro', function ($from, $to, $result) {
    [$from, $to] = [carbon($from), carbon($to)];

    expect($from->formatPeriodTo($to))->toBe($result);
})->with([
    ['1972-11-28', '1972-11-28', '1972-11-28'],
    ['2000-04-01', '2000-04-30', '2000-04'],
    ['2000-01-01', '2003-07-31', 'between 2000-01 and 2003-07'],
    ['2011-01-01', '2011-02-28', 'between 2011-01 and 2011-02'],
    ['1986-01-01', '1986-12-31', '1986'],
    ['1977-01-01', '1983-12-31', '1977-1983'],
    ['2002-12-17', '2015-10-31', 'between 2002-12-17 and 2015-10-31'],
]);

test('roman() helper', function ($arabic, $roman) {
    expect(roman($arabic))->toBe($roman);
})->with([
    [1, 'I'],
    [2, 'II'],
    [3, 'III'],
    [4, 'IV'],
    [5, 'V'],
    [6, 'VI'],
    [7, 'VII'],
    [8, 'VIII'],
    [9, 'IX'],
    [10, 'X'],
    [14, 'XIV'],
    [20, 'XX'],
    [23, 'XXIII'],
    [30, 'XXX'],
    [39, 'XXXIX'],
]);

test('faker() helper')
    ->expect(faker())
    ->toBeInstanceOf(\Faker\Generator::class);
