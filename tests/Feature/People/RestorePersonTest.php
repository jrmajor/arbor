<?php

namespace Tests\Feature\People;

use App\Models\Activity;
use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

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

        $this->assertTrue($this->person->fresh()->trashed());
    }

    #[TestDox('users without permissions cannot restore person')]
    public function testPermissions(): void
    {
        $this->withPermissions(2)
            ->patch("people/{$this->person->id}/restore")
            ->assertStatus(403);

        $this->assertTrue($this->person->fresh()->trashed());
    }

    #[TestDox('users with permissions can restore person')]
    public function testOk(): void
    {
        $this->withPermissions(3)
            ->patch("people/{$this->person->id}/restore")
            ->assertStatus(302)
            ->assertRedirect("people/{$this->person->id}");

        $this->assertFalse($this->person->fresh()->trashed());
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

        $log = Activity::newest();
        $this->assertSame('people', $log->log_name);
        $this->assertSame('restored', $log->description);
        $this->assertSameModel($this->person, $log->subject);

        $this->assertSame(
            ['attributes' => ['deleted_at' => null]],
            $log->properties->all(),
        );
    }
}
