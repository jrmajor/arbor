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
            ->assertStatus(302)
            ->assertRedirect('login');

        expect($this->person->fresh()->isVisible())->toBeFalse();
    }

    #[TestDox('users without permissions cannot change persons visibility')]
    public function testPermissions(): void
    {
        $this->withPermissions(3)
            ->put("people/{$this->person->id}/visibility")
            ->assertStatus(403);

        expect($this->person->fresh()->isVisible())->toBeFalse();
    }

    #[TestDox('users with permissions can change persons visibility')]
    public function testOk(): void
    {
        expect($this->person->isVisible())->toBeFalse();

        $this->withPermissions(4)
            ->from("people/{$this->person->id}/edit")
            ->put("people/{$this->person->id}/visibility", [
                'visibility' => true,
            ])->assertStatus(302)
            ->assertRedirect("people/{$this->person->id}/edit");

        expect($this->person->fresh()->isVisible())->toBeTrue();
    }

    #[TestDox('visibility change is logged')]
    public function testLogging(): void
    {
        expect($this->person->isVisible())->toBeFalse();

        $count = Activity::count();

        $this->travel(5)->minutes();

        $this->withPermissions(4)
            ->put("people/{$this->person->id}/visibility", [
                'visibility' => true,
            ]);

        $this->travelBack();

        expect(Activity::count())->toBe($count + 2); // visibility change and user creation

        $log = Activity::newest();
        $this->assertSame('people', $log->log_name);
        $this->assertSame('changed-visibility', $log->description);
        $this->assertSameModel($this->person, $log->subject);

        expect((string) $log->created_at)->toBe((string) $this->person->fresh()->updated_at);

        expect($log->properties->all())->toBe([
            'old' => ['visibility' => false],
            'attributes' => ['visibility' => true],
        ]);
    }
}
