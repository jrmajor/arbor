<?php

namespace Tests\Unit\Person\Relations;

use App\Models\Person;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class SiblingsTest extends TestCase
{
    #[TestDox('it can get siblings and half siblings')]
    public function testGet(): void
    {
        /** @var Person $person */
        $person = Person::factory(3)->create([
            'mother_id' => $mother = Person::factory()->female()->create(),
            'father_id' => $father = Person::factory()->male()->create(),
        ])->first();

        Person::factory()->withPersonParents()->create(['mother_id' => $mother]);
        Person::factory(2)->withoutPersonParents()->create(['mother_id' => $mother]);
        Person::factory(3)->withPersonParents()->create(['father_id' => $father]);
        Person::factory()->withoutPersonParents()->create(['father_id' => $father]);

        $this->assertCount(2, $person->siblings);
        $this->assertCount(3, $person->siblings_mother);
        $this->assertCount(4, $person->siblings_father);

        $person->mother_id = null;
        tap($person)->save()->refresh();

        $this->assertCount(0, $person->siblings);
        $this->assertCount(0, $person->siblings_mother);
        $this->assertCount(6, $person->siblings_father);
    }

    #[TestDox('it can eagerly get siblings and half siblings')]
    public function testEagerGet(): void
    {
        Model::preventLazyLoading(false);

        [$firstPerson] = Person::factory(3)->create([
            'mother_id' => $firstMother = Person::factory()->female()->create(),
            'father_id' => $firstFather = Person::factory()->male()->create(),
        ]);

        Person::factory()->withPersonParents()->create(['mother_id' => $firstMother]);
        Person::factory(2)->withoutPersonParents()->create(['mother_id' => $firstMother]);
        Person::factory(3)->withPersonParents()->create(['father_id' => $firstFather]);
        Person::factory()->withoutPersonParents()->create(['father_id' => $firstFather]);

        [$secondPerson] = Person::factory(4)->create([
            'mother_id' => $secondMother = Person::factory()->female()->create(),
            'father_id' => $secondFather = Person::factory()->male()->create(),
        ]);

        Person::factory(3)->withPersonParents()->create(['mother_id' => $secondMother]);
        Person::factory(2)->withoutPersonParents()->create(['mother_id' => $secondMother]);
        Person::factory(2)->withPersonParents()->create(['father_id' => $secondFather]);
        Person::factory(4)->withoutPersonParents()->create(['father_id' => $secondFather]);

        $people = Person::query()
            ->whereIn('id', [$firstPerson->id, $secondPerson->id])
            ->with('siblings', 'siblings_mother', 'siblings_father')->get();

        $this->assertCount(2, $people[0]->siblings);
        $this->assertCount(3, $people[0]->siblings_mother);
        $this->assertCount(4, $people[0]->siblings_father);

        $this->assertCount(3, $people[1]->siblings);
        $this->assertCount(5, $people[1]->siblings_mother);
        $this->assertCount(6, $people[1]->siblings_father);

        $firstPerson->update(['mother_id' => null]);

        $secondPerson->update([
            'mother_id' => null,
            'father_id' => null,
        ]);

        $people = Person::query()
            ->whereIn('id', [$firstPerson->id, $secondPerson->id])
            ->with('siblings')->get();

        $this->assertCount(0, $people[0]->siblings);
        $this->assertCount(0, $people[0]->siblings_mother);
        $this->assertCount(6, $people[0]->siblings_father);

        $this->assertCount(0, $people[1]->siblings);
        $this->assertCount(0, $people[1]->siblings_mother);
        $this->assertCount(0, $people[1]->siblings_father);
    }
}
