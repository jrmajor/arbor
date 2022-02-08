<?php

namespace Tests\Unit\Person;

use App\Models\Person;
use Exception;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class VisibilityTest extends TestCase
{
    #[TestDox('it can determine its visibility')]
    public function testDetermine(): void
    {
        $alive = Person::factory()->alive()->create();
        $dead = Person::factory()->dead()->create();

        $alive->visibility = false;
        $this->assertFalse($alive->isVisible());
        $alive->visibility = true;
        $this->assertTrue($alive->isVisible());

        $dead->visibility = false;
        $this->assertFalse($dead->isVisible());
        $dead->visibility = true;
        $this->assertTrue($dead->isVisible());
    }

    #[TestDox('visibility can be updated')]
    public function testUpdate(): void
    {
        $person = Person::factory()->create(['visibility' => false]);

        $person->fill(['visibility' => true])->save();
        $this->assertFalse($person->visibility);

        $person->forceFill(['visibility' => true])->save();
        $this->assertTrue($person->visibility);
    }

    #[TestDox('visibility can not be updated with other attributes')]
    public function testUpdateMany(): void
    {
        $person = Person::factory()->create([
            'name' => 'Old Name',
            'visibility' => false,
        ]);

        $this->expectException(Exception::class);

        $person->forceFill([
            'name' => 'New Name',
            'visibility' => true,
        ])->save();
    }
}
