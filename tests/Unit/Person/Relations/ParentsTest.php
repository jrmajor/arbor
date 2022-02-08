<?php

namespace Tests\Unit\Person\Relations;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class ParentsTest extends TestCase
{
    #[TestDox('it can get father')]
    public function testFather(): void
    {
        $father = Person::factory()->male()->create();
        $person = Person::factory()->create(['father_id' => $father]);

        $this->assertSameModel($father, $person->father);
    }

    #[TestDox('it can get mother')]
    public function testMother(): void
    {
        $mother = Person::factory()->female()->create();
        $person = Person::factory()->create(['mother_id' => $mother]);

        $this->assertSameModel($mother, $person->mother);
    }
}
