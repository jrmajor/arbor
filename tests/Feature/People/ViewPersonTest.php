<?php

namespace Tests\Feature\People;

use App\Person;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewPersonTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestCannotSeeHiddenAlivePerson()
    {
        $person = factory(Person::class)->states('alive')->create();

        $response = $this->get("people/$person->id");

        $response->assertStatus(403);
    }

    public function testGuestCanSeeDeadPerson()
    {
        $person = factory(Person::class)->states('dead')->create();

        $response = $this->get("people/$person->id");

        $response->assertStatus(200);
    }

    public function testGuestCanSeeVisibleAlivePerson()
    {
        $person = factory(Person::class)->states('alive')->create([
            'visibility' => 1,
        ]);

        $response = $this->get("people/$person->id");

        $response->assertStatus(200);
    }

    public function testUserWithPersmissionsCanSeeHiddenAlivePerson()
    {
        $user = factory(User::class)->create([
            'permissions' => 1,
        ]);

        $person = factory(Person::class)->states('alive')->create();

        $response = $this->actingAs($user)->get("people/$person->id");

        $response->assertStatus(200);
    }

    public function testUserWithPersmissionsCanSeeDeadPerson()
    {
        $user = factory(User::class)->create([
            'permissions' => 1,
        ]);

        $person = factory(Person::class)->states('dead')->create();

        $response = $this->actingAs($user)->get("people/$person->id");

        $response->assertStatus(200);
    }

    public function testUserWithPersmissionsCanSeeVisibleAlivePerson()
    {
        $user = factory(User::class)->create([
            'permissions' => 1,
        ]);

        $person = factory(Person::class)->states('alive')->create([
            'visibility' => 1,
        ]);

        $response = $this->actingAs($user)->get("people/$person->id");

        $response->assertStatus(200);
    }

    public function testGuestSee_404WhenAttemtingToViewNonexistentPerson()
    {
        $response = $this->get("people/1");

        $response->assertStatus(404);
    }

    public function testUserWithPersmissionsSee_404WhenAttemtingToViewNonexistentPerson()
    {
        $user = factory(User::class)->create([
            'permissions' => 1,
        ]);

        $response = $this->actingAs($user)->get("people/1");

        $response->assertStatus(404);
    }
}
