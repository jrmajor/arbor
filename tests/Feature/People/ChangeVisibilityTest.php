<?php

namespace Tests\Feature\People;

use App\Person;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class ChangeVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestsCannotChangePersonsVisibility()
    {
        $person = factory(Person::class)->create();

        $response = $this->put("people/$person->id/visibility");

        $response->assertStatus(302);
        $response->assertRedirect('login');

        $this->assertFalse($person->isVisible());
    }

    public function testUsersWithoutPermissionsCannotChangePersonsVisibility()
    {
        $person = factory(Person::class)->create();

        $user = factory(User::class)->create([
            'permissions' => 3,
        ]);

        $response = $this->actingAs($user)->put("people/$person->id/visibility");

        $response->assertStatus(403);

        $this->assertFalse($person->isVisible());
    }

    public function testUsersWithPermissionsCanChangePersonsVisibility()
    {
        $person = factory(Person::class)->create();

        $this->assertFalse($person->isVisible());

        $user = factory(User::class)->create([
            'permissions' => 4,
        ]);

        $response = $this->actingAs($user)->put("people/$person->id/visibility", [
            'visibility' => true,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect("people/$person->id");

        $this->assertTrue($person->fresh()->isVisible());
    }

    public function testVisibilityChangeIsLogged()
    {

        $person = factory(Person::class)->create();

        $this->assertFalse($person->isVisible());

        $user = factory(User::class)->create([
            'permissions' => 4,
        ]);

        Activity::all()->each->delete();

        $response = $this->actingAs($user)->put("people/$person->id/visibility", [
            'visibility' => true,
        ]);

        $this->assertCount(1, Activity::all());
        $log = $this->latestLog();

        $this->assertEquals('people', $log->log_name);
        $this->assertEquals('changed-visibility', $log->description);
        $this->assertTrue($person->is($log->subject));

        $this->assertEquals($person->updated_at, $log->created_at);

        $this->assertEquals(2, count($log->properties));
        $this->assertEquals(1, count($log->properties['old']));
        $this->assertEquals(1, count($log->properties['attributes']));

        $this->assertEquals(false, $log->properties['old']['visibility']);
        $this->assertEquals(true, $log->properties['attributes']['visibility']);
    }
}

