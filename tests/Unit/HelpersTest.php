<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    public function testFormatDateFromPeriod()
    {
        $this->assertEquals(
            '1972-11-28',
            format_date_from_period(new Carbon('1972-11-28'), new Carbon('1972-11-28'))
        );

        $this->assertEquals(
            '2000-04',
            format_date_from_period(new Carbon('2000-04-01'), new Carbon('2000-04-30'))
        );

        $this->assertEquals(
            __('misc.date.between') . ' 2000-01 ' . __('misc.date.and') . ' 2003-07',
            format_date_from_period(new Carbon('2000-01-01'), new Carbon('2003-07-31'))
        );

        $this->assertEquals(
            __('misc.date.between') . ' 2011-01 ' . __('misc.date.and') . ' 2011-02',
            format_date_from_period(new Carbon('2011-01-01'), new Carbon('2011-02-28'))
        );

        $this->assertEquals(
            '1986',
            format_date_from_period(new Carbon('1986-01-01'), new Carbon('1986-12-31'))
        );

        $this->assertEquals(
            '1977-1983',
            format_date_from_period(new Carbon('1977-01-01'), new Carbon('1983-12-31'))
        );

        $this->assertEquals(
            __('misc.date.between') . ' 2002-12-17 ' . __('misc.date.and') . ' 2015-10-31',
            format_date_from_period(new Carbon('2002-12-17'), new Carbon('2015-10-31'))
        );
}

    public function testRomanHelper()
    {
        $this->assertEquals('I', roman(1));
        $this->assertEquals('II', roman(2));
        $this->assertEquals('III', roman(3));
        $this->assertEquals('IV', roman(4));
        $this->assertEquals('V', roman(5));
        $this->assertEquals('VI', roman(6));
        $this->assertEquals('VII', roman(7));
        $this->assertEquals('VIII', roman(8));
        $this->assertEquals('IX', roman(9));
        $this->assertEquals('X', roman(10));
        $this->assertEquals('XIV', roman(14));
        $this->assertEquals('XX', roman(20));
        $this->assertEquals('XXIII', roman(23));
        $this->assertEquals('XXX', roman(30));
        $this->assertEquals('XXXIX', roman(39));
    }

    public function testFakerHelperReturnsFaker()
    {
        $faker = faker();

        $this->assertTrue($faker instanceof \Faker\Generator);
    }
}
