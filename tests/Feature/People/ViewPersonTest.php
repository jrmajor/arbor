<?php

namespace Tests\Feature\People;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class ViewPersonTest extends TestCase
{
    #[TestDox('guest cannot see hidden alive person')]
    public function testGuestHiddenAlive(): void
    {
        $person = Person::factory()->alive()->create();

        $this->get("people/{$person->id}")
            ->assertForbidden();
    }

    #[TestDox('guest cannot see hidden dead person')]
    public function testGuestHiddenDead(): void
    {
        $person = Person::factory()->dead()->create();

        $this->get("people/{$person->id}")
            ->assertForbidden();
    }

    #[TestDox('guest can see visible alive person')]
    public function testGuestVisibleAlive(): void
    {
        $person = Person::factory()->alive()->create([
            'visibility' => true,
        ]);

        $this->get("people/{$person->id}")
            ->assertOk();
    }

    #[TestDox('guest can see visible dead person')]
    public function testGuestVisibleDead(): void
    {
        $person = Person::factory()->dead()->create([
            'visibility' => true,
        ]);

        $this->get("people/{$person->id}")
            ->assertOk();
    }

    #[TestDox('user with permissions can see hidden alive person')]
    public function testUserHiddenAlive(): void
    {
        $person = Person::factory()->alive()->create();

        $this->withPermissions(1)
            ->get("people/{$person->id}")
            ->assertOk();
    }

    #[TestDox('user with permissions can see hidden dead person')]
    public function testUserHiddenDead(): void
    {
        $person = Person::factory()->dead()->create();

        $this->withPermissions(1)
            ->get("people/{$person->id}")
            ->assertOk();
    }

    #[TestDox('user with permissions can see visible alive person')]
    public function testUserVisibleAlive(): void
    {
        $person = Person::factory()->alive()->create([
            'visibility' => true,
        ]);

        $this->withPermissions(1)
            ->get("people/{$person->id}")
            ->assertOk();
    }

    #[TestDox('user with permissions can see visible dead person')]
    public function testUserVisibleDead(): void
    {
        $person = Person::factory()->dead()->create([
            'visibility' => true,
        ]);

        $this->withPermissions(1)
            ->get("people/{$person->id}")
            ->assertOk();
    }

    #[TestDox('guest see 404 when attempting to view nonexistent person')]
    public function testGuestNonexistent(): void
    {
        $this->get('people/1')
            ->assertNotFound();
    }

    #[TestDox('user with insufficient permissions see 404 when attempting to view nonexistent person')]
    public function testPermissionsNonexistent(): void
    {
        $this->withPermissions(1)
            ->get('people/1')
            ->assertNotFound();
    }

    #[TestDox('guest see 404 when attempting to view deleted person')]
    public function testGuestDeleted(): void
    {
        $person = Person::factory()->create(['deleted_at' => now()]);

        $this->get("people/{$person->id}")
            ->assertNotFound();
    }

    #[TestDox('users without permissions see 404 when attempting to view deleted person')]
    public function testPermissionsDeleted(): void
    {
        $person = Person::factory()->create(['deleted_at' => now()]);

        $this->withPermissions(2)
            ->get("people/{$person->id}")
            ->assertNotFound();
    }

    #[TestDox('user with permissions are redirected to edits history when attempting to view deleted person')]
    public function testRedirectDeleted(): void
    {
        $person = Person::factory()->create(['deleted_at' => now()]);

        $this->withPermissions(3)
            ->get("people/{$person->id}")
            ->assertFound()
            ->assertRedirect("people/{$person->id}/history");
    }
}
