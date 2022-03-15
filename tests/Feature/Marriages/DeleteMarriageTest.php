<?php

namespace Tests\Feature\Marriages;

use App\Models\Activity;
use App\Models\Marriage;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class DeleteMarriageTest extends TestCase
{
    private Marriage $marriage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->marriage = Marriage::factory()->create();
    }

    #[TestDox('guests cannot delete marriage')]
    public function testGuest(): void
    {
        $this->delete("marriages/{$this->marriage->id}")
            ->assertStatus(302)
            ->assertRedirect('login');

        $this->assertFalse($this->marriage->fresh()->trashed());
    }

    #[TestDox('users without permissions cannot delete marriage')]
    public function testPermissions(): void
    {
        $this->withPermissions(1)
            ->delete("marriages/{$this->marriage->id}")
            ->assertStatus(403);

        $this->assertFalse($this->marriage->fresh()->trashed());
    }

    #[TestDox('users with permissions can delete marriage')]
    public function testOk(): void
    {
        $this->withPermissions(2)
            ->delete("marriages/{$this->marriage->id}")
            ->assertStatus(302);

        $this->assertTrue($this->marriage->fresh()->trashed());
    }

    #[TestDox('users without permissions to view history are redirected to woman page')]
    public function testRedirectPermissions(): void
    {
        $this->withPermissions(2)
            ->delete("marriages/{$this->marriage->id}")
            ->assertStatus(302)
            ->assertRedirect("people/{$this->marriage->woman_id}");
    }

    #[TestDox('users with permissions to view history are redirected to marriage history')]
    public function testRedirect(): void
    {
        $this->withPermissions(3)
            ->delete("marriages/{$this->marriage->id}")
            ->assertStatus(302)
            ->assertRedirect("marriages/{$this->marriage->id}/history");
    }

    #[TestDox('marriage deletion is logged')]
    public function testLogging(): void
    {
        $this->marriage->delete();

        $log = Activity::newest();
        $this->assertSame('marriages', $log->log_name);
        $this->assertSame('deleted', $log->description);
        $this->assertSameModel($this->marriage, $log->subject);

        $this->assertSame((string) $this->marriage->deleted_at, (string) $log->created_at);

        $this->assertSame(
            ['attributes' => ['deleted_at' => $this->marriage->deleted_at->toJson()]],
            $log->properties->all(),
        );
    }
}
