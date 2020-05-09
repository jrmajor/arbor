<?php

namespace Tests\Feature\Marriages;

use App\Marriage;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteMarriageTest extends TestCase
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

    public function testMarriageDeletionIsLogged()
    {
        $marriage = factory(Marriage::class)->create();

        $marriage->delete();

        $log = $this->latestLog();

        $this->assertEquals('marriages', $log->log_name);
        $this->assertEquals('deleted', $log->description);
        $this->assertTrue($marriage->is($log->subject));

        $this->assertEquals($marriage->deleted_at, $log->created_at);

        $this->assertEquals(
            $marriage->deleted_at,
            Carbon::create($log->properties['attributes']['deleted_at'])
        );

        $this->assertEquals(1, count($log->properties));
        $this->assertEquals(1, count($log->properties['attributes']));
    }
}
