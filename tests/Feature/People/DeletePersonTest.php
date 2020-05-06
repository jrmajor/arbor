<?php

namespace Tests\Feature\People;

use App\Person;
use App\User;
use Carbon\Carbon;
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

    public function testUsersWithoutPermissionsCannotDeletePerson()
    {
        $person = factory(Person::class)->create();

        $user = factory(User::class)->create([
            'permissions' => 1,
        ]);

        $response = $this->actingAs($user)->delete("people/$person->id");

        $response->assertStatus(403);

        $this->assertFalse($person->fresh()->trashed());
    }

    public function testUsersWithPermissionsCanDeletePerson()
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

    public function testPersonDeletionIsLogged()
    {
        $person = factory(Person::class)->create();

        $person->delete();

        $log = $this->latestLog();

        $this->assertEquals('people', $log->log_name);
        $this->assertEquals('deleted', $log->description);
        $this->assertTrue($person->is($log->subject));

        $this->assertEquals($person->deleted_at, $log->created_at);

        $this->assertEquals(
            $person->deleted_at,
            Carbon::create($log->properties['attributes']['deleted_at'])
        );

        $this->assertEquals(1, count($log->properties));
        $this->assertEquals(1, count($log->properties['attributes']));
    }
}

