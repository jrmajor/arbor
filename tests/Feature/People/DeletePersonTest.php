<?php

namespace Tests\Feature\People;

use App\Person;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletePersonTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestsCannotDeletePerson()
    {
        $person = factory(Person::class)->create();

        $response = $this->delete("people/$person->id");

        $response->assertStatus(302);
        $response->assertRedirect('login');

        $this->assertFalse($person->fresh()->trashed());
    }

    public function testUsersWithoutPermissionsCannotEditPerson()
    {
        $person = factory(Person::class)->create();

        $user = factory(User::class)->create([
            'permissions' => 1,
        ]);

        $response = $this->actingAs($user)->delete("people/$person->id");

        $response->assertStatus(403);

        $this->assertFalse($person->fresh()->trashed());
    }

    public function testUsersWithPermissionsCanEditPerson()
    {
        $person = factory(Person::class)->create();

        $user = factory(User::class)->create([
            'permissions' => 3,
        ]);

        $response = $this->actingAs($user)->delete("people/$person->id");

        $response->assertStatus(302);
        $response->assertRedirect("people");

        $this->assertTrue($person->fresh()->trashed());
    }
}

