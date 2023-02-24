<?php

namespace Tests\Unit;

use Carbon\Carbon;
use DateTime;
use Faker\Generator as FakerGenerator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class HelpersTest extends TestCase
{
    #[TestDox('carbon() helper works')]
    public function testCarbonHelper(): void
    {
        $this->assertInstanceOf(Carbon::class, carbon('2021-04-01'));

        $this->assertSame(
            '2005-04-02T21:37:00+02:00',
            carbon('2005-04-02 21:37')->format(DateTime::ATOM),
        );
    }

    #[DataProvider('provideFormatPeriodCases')]
    #[TestDox('format period from dates carbon macro')]
    public function testFormatPeriodTo(string $from, string $to, string $result): void
    {
        [$from, $to] = [carbon($from), carbon($to)];

        $this->assertSame($result, $from->formatPeriodTo($to));
    }

    private static function provideFormatPeriodCases(): Generator
    {
        yield from [
            ['1972-11-28', '1972-11-28', '1972-11-28'],
            ['2000-04-01', '2000-04-30', '2000-04'],
            ['2000-01-01', '2003-07-31', 'between 2000-01 and 2003-07'],
            ['2011-01-01', '2011-02-28', 'between 2011-01 and 2011-02'],
            ['1986-01-01', '1986-12-31', '1986'],
            ['1977-01-01', '1983-12-31', '1977-1983'],
            ['2002-12-17', '2015-10-31', 'between 2002-12-17 and 2015-10-31'],
        ];
    }

    #[DataProvider('provideRomanCases')]
    #[TestDox('roman() helper works')]
    public function testRomanHelper(int $arabic, string $roman): void
    {
        $this->assertSame($roman, roman($arabic));
    }

    private static function provideRomanCases(): Generator
    {
        yield from [
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
        ];
    }

    #[TestDox('faker() helper works')]
    public function testFakerHelper(): void
    {
        /** @phpstan-ignore-next-line */
        $this->assertInstanceOf(FakerGenerator::class, faker());
    }
}
