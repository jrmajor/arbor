<?php

namespace Tests\Unit\Marriage;

use App\Models\Marriage;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class VisibilityTest extends TestCase
{
    #[TestDox('it can determine its visibility')]
    public function testVisibility(): void
    {
        $marriage = Marriage::factory()->create();
        $this->assertFalse($marriage->isVisible());

        $marriage->woman->visibility = true;
        $this->assertFalse($marriage->isVisible());

        $marriage->man->visibility = true;
        $this->assertTrue($marriage->isVisible());
    }
}
