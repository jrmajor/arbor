<?php

namespace Tests\Feature\People;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class ViewPersonEditsHistoryTest extends TestCase
{
    private Person $person;

    protected function setUp(): void
    {
        parent::setUp();

        $this->person = Person::factory()->create();
    }

    #[TestDox('guests are asked to log in when attempting to view person history')]
    public function testGuest(): void
    {
        $this->get("people/{$this->person->id}/history")
            ->assertFound()
            ->assertRedirect('login');
    }

    #[TestDox('users without permissions cannot view person history')]
    public function testPermissions(): void
    {
        $this->withPermissions(2)
            ->get("people/{$this->person->id}/history")
            ->assertForbidden();
    }

    #[TestDox('users with permissions can view person history')]
    public function testOk(): void
    {
        $this->withPermissions(3)
            ->get("people/{$this->person->id}/history")
            ->assertInertiaComponent('People/History');
    }
}
