<?php

namespace Tests\Unit\Person\Relations;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class ChildrenTest extends TestCase
{
    #[TestDox('it can get children')]
    public function testGet(): void
    {
        $father = Person::factory()->male()->create();

        Person::factory(2)->withPersonParents()->create(['father_id' => $father]);
        Person::factory()->withoutPersonParents()->create(['father_id' => $father]);

        $this->assertCount(3, $father->children);
    }

    #[TestDox('it can eagerly get children')]
    public function testEagerGet(): void
    {
        $mother = Person::factory()->female()->create();

        Person::factory(3)->withoutPersonParents()->create(['mother_id' => $mother]);
        Person::factory(2)->withPersonParents()->create(['mother_id' => $mother]);

        $father = Person::factory()->male()->create();

        Person::factory()->withoutPersonParents()->create(['father_id' => $father]);
        Person::factory(2)->withPersonParents()->create(['father_id' => $father]);

        [$mother, $father] = Person::query()
            ->whereIn('id', [$mother->id, $father->id])
            ->with('children')->get();

        $this->assertCount(5, $mother->children);
        $this->assertCount(3, $father->children);
    }
}
