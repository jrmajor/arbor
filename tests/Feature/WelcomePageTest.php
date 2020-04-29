<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    public function testItCanBeAccessedByGuests()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function testItRedirectsLoggedUsers()
    {
        $this->actingAs(factory(User::class)->create());

        $response = $this->get('/');

        $response->assertRedirect('/people');
    }
}
