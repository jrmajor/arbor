<?php

namespace Tests\Feature\People;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function Tests\latestLog;

final class RestorePersonTest extends TestCase
{
    private Person $person;

    protected function setUp(): void
    {
        parent::setUp();

        $this->person = Person::factory()->create(['deleted_at' => now()]);
    }

    #[TestDox('guests cannot restore person')]
    public function testGuest(): void
    {
        $this->patch("people/{$this->person->id}/restore")
            ->assertStatus(302)
            ->assertRedirect('login');

        expect($this->person->fresh()->trashed())->toBeTrue();
    }

    #[TestDox('users without permissions cannot restore person')]
    public function testPermissions(): void
    {
        $this->withPermissions(2)
            ->patch("people/{$this->person->id}/restore")
            ->assertStatus(403);

        expect($this->person->fresh()->trashed())->toBeTrue();
    }

    #[TestDox('users with permissions can restore person')]
    public function testOk(): void
    {
        $this->withPermissions(3)
            ->patch("people/{$this->person->id}/restore")
            ->assertStatus(302)
            ->assertRedirect("people/{$this->person->id}");

        expect($this->person->fresh()->trashed())->toBeFalse();
    }

    #[TestDox('person can be restored only when deleted')]
    public function testDeleted(): void
    {
        $this->person->restore();

        $this->withPermissions(3)
            ->patch("people/{$this->person->id}/restore")
            ->assertStatus(404);
    }

    #[TestDox('person restoration is logged')]
    public function testLogging(): void
    {
        $this->person->restore();

        expect($log = latestLog())
            ->log_name->toBe('people')
            ->description->toBe('restored')
            ->subject->toBeModel($this->person);

        expect($log->properties->all())->toBe([
            'attributes' => ['deleted_at' => null],
        ]);
    }
}
