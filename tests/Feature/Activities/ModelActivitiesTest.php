<?php

namespace Tests\Feature\Activities;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModelActivitiesTest extends TestCase
{
    public function testGuestAreAskedToLogInWhenAttemptingToViewModelActivites()
    {
        $response = $this->get('activities/models');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function testUsersWithoutPermissionsCannotViewModelActivitess()
    {
        $user = factory(User::class)->create([
            'permissions' => 3,
        ]);

        $response = $this->actingAs($user)->get('activities/models');

        $response->assertStatus(403);
    }

    public function testUsersWithPermissionsCanViewModelActivites()
    {
        $user = factory(User::class)->create([
            'permissions' => 4,
        ]);

        $response = $this->actingAs($user)->get('activities/models');

        $response->assertStatus(200);
    }
}
