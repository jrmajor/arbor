<?php

namespace Tests\Feature\Marriages;

use App\Marriage;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarriagesDeleteMarriageTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestsCannotDeleteMarriage()
    {
        $marriage = factory(Marriage::class)->create();

        $response = $this->delete("marriages/$marriage->id");

        $response->assertStatus(302);
        $response->assertRedirect('login');

        $this->assertFalse($marriage->fresh()->trashed());
    }

    public function testUsersWithoutPermissionsCannotEditMarriage()
    {
        $marriage = factory(Marriage::class)->create();

        $user = factory(User::class)->create([
            'permissions' => 1,
        ]);

        $response = $this->actingAs($user)->delete("marriages/$marriage->id");

        $response->assertStatus(403);

        $this->assertFalse($marriage->fresh()->trashed());
    }

    public function testUsersWithPermissionsCanEditMarriage()
    {
        $marriage = factory(Marriage::class)->create();

        $user = factory(User::class)->create([
            'permissions' => 3,
        ]);

        $response = $this->actingAs($user)->delete("marriages/$marriage->id");

        $response->assertStatus(302);
        $response->assertRedirect("people/$marriage->woman_id");

        $this->assertTrue($marriage->fresh()->trashed());
    }
}

