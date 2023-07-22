<?php

namespace Tests\Feature\Marriages;

use App\Models\Marriage;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class ViewMarriageEditsHistoryTest extends TestCase
{
    private Marriage $marriage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->marriage = Marriage::factory()->create();
    }

    #[TestDox('guests are asked to log in when attempting to view marriage history')]
    public function testGuest(): void
    {
        $this->get("marriages/{$this->marriage->id}/history")
            ->assertFound()
            ->assertRedirect('login');
    }

    #[TestDox('users without permissions cannot view marriage history')]
    public function testPermissions(): void
    {
        $this->withPermissions(2)
            ->get("marriages/{$this->marriage->id}/history")
            ->assertForbidden();
    }

    #[TestDox('users with permissions can view marriage history')]
    public function testOk(): void
    {
        $this->withPermissions(3)
            ->get("marriages/{$this->marriage->id}/history")
            ->assertOk();
    }
}
