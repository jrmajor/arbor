<?php

namespace Tests\Feature\People;

use App\Models\Activity;
use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class ChangeVisibilityTest extends TestCase
{
    private Person $person;

    protected function setUp(): void
    {
        parent::setUp();

        $this->person = Person::factory()->create();
    }

    #[TestDox('guests cannot change persons visibility')]
    public function testGuest(): void
    {
        $this->put("people/{$this->person->id}/visibility")
            ->assertFound()
            ->assertRedirect('login');

        $this->assertFalse($this->person->fresh()->isVisible());
    }

    #[TestDox('users without permissions cannot change persons visibility')]
    public function testPermissions(): void
    {
        $this->withPermissions(3)
            ->put("people/{$this->person->id}/visibility")
            ->assertForbidden();

        $this->assertFalse($this->person->fresh()->isVisible());
    }

    #[TestDox('users with permissions can change persons visibility')]
    public function testOk(): void
    {
        $this->assertFalse($this->person->isVisible());

        $this->withPermissions(4)
            ->from("people/{$this->person->id}/edit")
            ->put("people/{$this->person->id}/visibility", [
                'visibility' => true,
            ])->assertFound()
            ->assertRedirect("people/{$this->person->id}/edit");

        $this->assertTrue($this->person->fresh()->isVisible());
    }

    #[TestDox('visibility change is logged')]
    public function testLogging(): void
    {
        $this->assertFalse($this->person->isVisible());

        $count = Activity::count();

        $this->travel(5)->minutes();

        $this->withPermissions(4)
            ->put("people/{$this->person->id}/visibility", [
                'visibility' => true,
            ]);

        $this->travelBack();

        $this->assertSame($count + 2, Activity::count()); // visibility change and user creation

        $log = Activity::newest();
        $this->assertSame('people', $log->log_name);
        $this->assertSame('changed-visibility', $log->description);
        $this->assertSameModel($this->person, $log->subject);

        $this->assertSame((string) $this->person->fresh()->updated_at, (string) $log->created_at);

        $this->assertSame([
            'old' => ['visibility' => false],
            'attributes' => ['visibility' => true],
        ], $log->properties->all());
    }
}
