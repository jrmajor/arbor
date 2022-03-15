<?php

namespace Tests\Feature\Marriages;

use App\Models\Marriage;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function Pest\Laravel\delete;
use function Tests\latestLog;

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
        delete("marriages/{$this->marriage->id}")
            ->assertStatus(302)
            ->assertRedirect('login');

        expect($this->marriage->fresh()->trashed())->toBeFalse();
    }

    #[TestDox('users without permissions cannot delete marriage')]
    public function testPermissions(): void
    {
        $this->withPermissions(1)
            ->delete("marriages/{$this->marriage->id}")
            ->assertStatus(403);

        expect($this->marriage->fresh()->trashed())->toBeFalse();
    }

    #[TestDox('users with permissions can delete marriage')]
    public function testOk(): void
    {
        $this->withPermissions(2)
            ->delete("marriages/{$this->marriage->id}")
            ->assertStatus(302);

        expect($this->marriage->fresh()->trashed())->toBeTrue();
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

        expect($log = latestLog())
            ->log_name->toBe('marriages')
            ->description->toBe('deleted')
            ->subject->toBeModel($this->marriage);

        expect((string) $log->created_at)->toBe((string) $this->marriage->deleted_at);

        expect($log->properties->all())->toBe([
            'attributes' => ['deleted_at' => $this->marriage->deleted_at->toJson()],
        ]);
    }
}
