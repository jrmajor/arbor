<?php

namespace Tests\Unit\Person\Relations;

use App\Models\Marriage;
use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class MarriagesTest extends TestCase
{
    #[TestDox('it can get marriages')]
    public function testGet(): void
    {
        $person = Person::factory()->female()->create();

        Marriage::factory(3)->create(['woman_id' => $person]);

        $this->assertCount(3, $person->marriages);
    }

    #[TestDox('it can eagerly get marriages')]
    public function testEagerGet(): void
    {
        $woman = Person::factory()->female()->create();
        $man = Person::factory()->male()->create();

        Marriage::factory(3)->create(['woman_id' => $woman]);
        Marriage::factory(4)->create(['man_id' => $man]);

        $people = Person::query()
            ->whereIn('id', [$woman->id, $man->id])
            ->with('marriages')
            ->get()
            ->keyBy('id');

        $this->assertCount(3, $people[$woman->id]->marriages);
        $this->assertCount(4, $people[$man->id]->marriages);
    }

    #[TestDox('it orders eagerly loaded marriages by the person\'s order')]
    public function testEagerOrder(): void
    {
        $woman = Person::factory()->female()->create();
        $man = Person::factory()->male()->create();

        $first = Marriage::factory()->create([
            'woman_id' => $woman, 'woman_order' => 2,
            'man_id' => $man, 'man_order' => 1,
        ]);
        $second = Marriage::factory()->create(['woman_id' => $woman, 'woman_order' => 1]);
        $third = Marriage::factory()->create(['man_id' => $man, 'man_order' => 2]);

        $people = Person::query()
            ->whereIn('id', [$woman->id, $man->id])
            ->with('marriages')
            ->get()
            ->keyBy('id');

        $this->assertSame([$second->id, $first->id], $people[$woman->id]->marriages->pluck('id')->all());
        $this->assertSame([$first->id, $third->id], $people[$man->id]->marriages->pluck('id')->all());
    }
}
