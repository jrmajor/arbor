<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function App\trim_values;

final class TrimValuesTest extends TestCase
{
    #[TestDox('it works with string keys')]
    public function testStringKeys(): void
    {
        $this->assertSame(
            ['foo' => null, 'bar' => null, 'baz' => 'test'],
            trim_values(['foo' => null, 'bar' => "\t  \n", 'baz' => "\t test \n"]),
        );
    }

    #[TestDox('it works with integer keys')]
    public function testIntKeys(): void
    {
        $this->assertSame(
            [null, null, 'test'],
            trim_values([null, "\t  \n", "\t test \n"]),
        );
    }
}
