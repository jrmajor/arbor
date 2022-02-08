<?php

namespace Tests\Unit\Person\Relations;

use App\Models\Marriage;
use App\Models\Person;
use Illuminate\Database\Eloquent\Model;
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
        Model::preventLazyLoading(false);

        $woman = Person::factory()->female()->create();
        $man = Person::factory()->male()->create();

        Marriage::factory(3)->create(['woman_id' => $woman]);
        Marriage::factory(4)->create(['man_id' => $man]);

        $people = Person::query()
            ->whereIn('id', [$woman->id, $man->id])
            ->with('children')->get();

        $this->assertCount(3, $people[0]->marriages);
        $this->assertCount(4, $people[1]->marriages);
    }
}
