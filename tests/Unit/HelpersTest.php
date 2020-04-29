<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function testFormatDateHelper()
    {
        $this->assertEquals('1984', format_date('1984-00-00'));
        $this->assertEquals('1963-05', format_date('1963-05-00'));
        $this->assertEquals('1984-07-14', format_date('1984-07-14'));
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
