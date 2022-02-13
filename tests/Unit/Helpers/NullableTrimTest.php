<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function App\nullable_trim;

final class NullableTrimTest extends TestCase
{
    #[TestDox('it works with null argument')]
    public function testNull(): void
    {
        $this->assertNull(nullable_trim(null));
    }

    #[TestDox('it works with whitespace-only string')]
    public function testWhitespace(): void
    {
        $this->assertNull(nullable_trim("\t  \n"));
    }

    #[TestDox('it works with non-empty string')]
    public function testString(): void
    {
        $this->assertSame('nullable trim test', nullable_trim("\t nullable trim test \n"));
    }
}
