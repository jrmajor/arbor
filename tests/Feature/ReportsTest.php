<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    public function testGuestAreAskedToLogInWhenAttemptingToViewReports()
    {
        $response = $this->get('reports');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function testUsersWithoutPermissionsCannotViewReports()
    {
        $user = factory(User::class)->create([
            'permissions' => 3,
        ]);

        $response = $this->actingAs($user)->get('reports');

        $response->assertStatus(403);
    }

    public function testUsersWithPermissionsCanViewReports()
    {
        $user = factory(User::class)->create([
            'permissions' => 4,
        ]);

        $response = $this->actingAs($user)->get('reports');

        $response->assertStatus(200);
    }
}
