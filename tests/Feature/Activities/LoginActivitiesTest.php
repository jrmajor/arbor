<?php

namespace Tests\Feature\Activities;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginActivitiesTest extends TestCase
{
    public function testGuestAreAskedToLogInWhenAttemptingToViewLoginActivites()
    {
        $response = $this->get('activities/logins');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function testUsersWithoutPermissionsCannotViewLoginActivitess()
    {
        $user = factory(User::class)->create([
            'permissions' => 3,
        ]);

        $response = $this->actingAs($user)->get('activities/logins');

        $response->assertStatus(403);
    }

    public function testUsersWithPermissionsCanViewLoginActivites()
    {
        $user = factory(User::class)->create([
            'permissions' => 4,
        ]);

        $response = $this->actingAs($user)->get('activities/logins');

        $response->assertStatus(200);
    }
}
