<?php

namespace Tests\Feature\People;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function Tests\latestLog;

final class DeletePersonTest extends TestCase
{
    private Person $person;

    protected function setUp(): void
    {
        parent::setUp();

        $this->person = Person::factory()->create();
    }

    #[TestDox('guests cannot delete person')]
    public function testGuest(): void
    {
        $this->delete("people/{$this->person->id}")
            ->assertStatus(302)
            ->assertRedirect('login');

        expect($this->person->fresh()->trashed())->toBeFalse();
    }

    #[TestDox('users without permissions cannot delete person')]
    public function testPermissions(): void
    {
        $this->withPermissions(1)
            ->delete("people/{$this->person->id}")
            ->assertStatus(403);

        expect($this->person->fresh()->trashed())->toBeFalse();
    }

    #[TestDox('users with permissions can delete person')]
    public function testOk(): void
    {
        $this->withPermissions(2)
            ->delete("people/{$this->person->id}")
            ->assertStatus(302);

        expect($this->person->fresh()->trashed())->toBeTrue();
    }

    #[TestDox('users without permissions to view history are redirected to people index')]
    public function testRedirectPermissions(): void
    {
        $this->withPermissions(2)
            ->delete("people/{$this->person->id}")
            ->assertStatus(302)
            ->assertRedirect('people');
    }

    #[TestDox('users with permissions to view history are redirected to person history')]
    public function testRedirect(): void
    {
        $this->withPermissions(3)
            ->delete("people/{$this->person->id}")
            ->assertStatus(302)
            ->assertRedirect("people/{$this->person->id}/history");
    }

    #[TestDox('person deletion is logged')]
    public function testLogging(): void
    {
        $this->person->delete();

        expect($log = latestLog())
            ->log_name->toBe('people')
            ->description->toBe('deleted')
            ->subject->toBeModel($this->person);

        expect((string) $log->created_at)->toBe((string) $this->person->deleted_at);

        expect($log->properties->all())->toBe([
            'attributes' => ['deleted_at' => $this->person->deleted_at->toJson()],
        ]);
    }
}
