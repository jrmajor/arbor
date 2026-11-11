<?php

namespace Tests\Feature\People;

use App\Models\Person;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class RelationshipTest extends TestCase
{
    #[TestDox('it shows the page without a second person')]
    public function testWithoutOther(): void
    {
        $person = Person::factory()->create(['visibility' => true]);

        $this->get("people/{$person->id}/relationship")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('People/Relationship')
                ->where('other', null)
                ->where('relationship', null)
                ->etc());
    }

    #[TestDox('it shows the path between two people')]
    public function testPath(): void
    {
        $father = Person::factory()->male()->create(['visibility' => false]);
        $person = Person::factory()->male()->create(['visibility' => true, 'father_id' => $father->id]);
        $sister = Person::factory()->female()->create(['visibility' => true, 'father_id' => $father->id]);

        $this->get("people/{$person->id}/relationship?to={$sister->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('other.id', $sister->id)
                ->where('relationship.blood', true)
                ->where('relationship.kinship.type', 'sibling')
                ->where('relationship.kinship.half', true)
                ->has('relationship.steps', 3)
                ->where('relationship.steps.1.person.id', $father->id)
                ->where('relationship.steps.1.person.visible', false)
                ->missing('relationship.steps.1.person.name')
                ->where('relationship.steps.1.step', 'parent')
                ->where('relationship.commonAncestors.0.id', $father->id)
                ->etc());
    }

    #[TestDox('it says when people are not related')]
    public function testUnrelated(): void
    {
        $person = Person::factory()->create(['visibility' => true]);
        $other = Person::factory()->create(['visibility' => true]);

        $this->get("people/{$person->id}/relationship?to={$other->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('other.id', $other->id)
                ->where('relationship', null)
                ->etc());
    }

    #[TestDox('guests cannot check relationships of hidden people')]
    public function testHidden(): void
    {
        $person = Person::factory()->create(['visibility' => true]);
        $hidden = Person::factory()->create(['visibility' => false]);

        $this->get("people/{$hidden->id}/relationship")->assertForbidden();
        $this->get("people/{$person->id}/relationship?to={$hidden->id}")->assertForbidden();

        $this->withPermissions(1)
            ->get("people/{$person->id}/relationship?to={$hidden->id}")
            ->assertOk();
    }
}
