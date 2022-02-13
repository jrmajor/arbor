<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function App\parse_int;

final class ParseIntTest extends TestCase
{
    #[TestDox('it works with null argument')]
    public function testNull(): void
    {
        $this->assertNull(parse_int(null));
    }

    #[TestDox('it works with whitespace-only string')]
    public function testWhitespace(): void
    {
        $this->assertNull(parse_int("\t  \n"));
    }

    #[TestDox('it works with non-numeric string')]
    public function testNonNumeric(): void
    {
        $this->assertNull(parse_int('parse int test'));
    }

    #[TestDox('it works with numeric string')]
    public function testString(): void
    {
        $this->assertSame(8, parse_int("\t 8 \n"));
    }
}
