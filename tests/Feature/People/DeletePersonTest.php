<?php

namespace Tests\Feature\People;

use App\Models\Activity;
use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

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

        $this->assertFalse($this->person->fresh()->trashed());
    }

    #[TestDox('users without permissions cannot delete person')]
    public function testPermissions(): void
    {
        $this->withPermissions(1)
            ->delete("people/{$this->person->id}")
            ->assertStatus(403);

        $this->assertFalse($this->person->fresh()->trashed());
    }

    #[TestDox('users with permissions can delete person')]
    public function testOk(): void
    {
        $this->withPermissions(2)
            ->delete("people/{$this->person->id}")
            ->assertStatus(302);

        $this->assertTrue($this->person->fresh()->trashed());
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

        $log = Activity::newest();
        $this->assertSame('people', $log->log_name);
        $this->assertSame('deleted', $log->description);
        $this->assertSameModel($this->person, $log->subject);

        $this->assertSame((string) $this->person->deleted_at, (string) $log->created_at);

        $this->assertSame(
            ['attributes' => ['deleted_at' => $this->person->deleted_at->toJson()]],
            $log->properties->all(),
        );
    }
}
