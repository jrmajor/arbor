<?php

namespace Tests\Feature\People;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function Pest\Laravel\get;

final class ViewPersonEditsHistoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->person = Person::factory()->create();
    }

    #[TestDox('guests are asked to log in when attempting to view person history')]
    public function testGuest(): void
    {
        get("people/{$this->person->id}/history")
            ->assertStatus(302)
            ->assertRedirect('login');
    }

    #[TestDox('users without permissions cannot view person history')]
    public function testPermissions(): void
    {
        $this->withPermissions(2)
            ->get("people/{$this->person->id}/history")
            ->assertStatus(403);
    }

    #[TestDox('users with permissions can view person history')]
    public function testOk(): void
    {
        $this->withPermissions(3)
            ->get("people/{$this->person->id}/history")
            ->assertStatus(200);
    }
}
