<?php

namespace Tests\Feature\Marriages;

use App\Models\Marriage;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function Tests\latestLog;

final class RestoreMarriageTest extends TestCase
{
    private Marriage $marriage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->marriage = Marriage::factory()->create(['deleted_at' => now()]);
    }

    #[TestDox('guests cannot restore marriage')]
    public function testGuest(): void
    {
        $this->patch("marriages/{$this->marriage->id}/restore")
            ->assertStatus(302)
            ->assertRedirect('login');

        expect($this->marriage->fresh()->trashed())->toBeTrue();
    }

    #[TestDox('users without permissions cannot restore marriage')]
    public function testPermissions(): void
    {
        $this->withPermissions(2)
            ->patch("marriages/{$this->marriage->id}/restore")
            ->assertStatus(403);

        expect($this->marriage->fresh()->trashed())->toBeTrue();
    }

    #[TestDox('users with permissions can restore marriage')]
    public function testOk(): void
    {
        $this->withPermissions(3)
            ->patch("marriages/{$this->marriage->id}/restore")
            ->assertStatus(302)
            ->assertRedirect("people/{$this->marriage->woman_id}");

        expect($this->marriage->fresh()->trashed())->toBeFalse();
    }

    #[TestDox('marriage can be restored only when deleted')]
    public function testDeleted(): void
    {
        $this->marriage->restore();

        $this->withPermissions(3)
            ->patch("marriages/{$this->marriage->id}/restore")
            ->assertStatus(404);
    }

    #[TestDox('marriage restoration is logged')]
    public function testLogging(): void
    {
        $this->marriage->restore();

        expect($log = latestLog())
            ->log_name->toBe('marriages')
            ->description->toBe('restored')
            ->subject->toBeModel($this->marriage);

        expect($log->properties->all())->toBe([
            'attributes' => ['deleted_at' => null],
        ]);
    }
}
