<?php

namespace Tests\Feature\Ajax;

use App\User;
use Tests\TestCase;

class AjaxPytlewskiTest extends TestCase
{
    public function testGuestsCannotAccessEndpoint()
    {
        $response = $this->postJson('/ajax/pytlewski', [
            'search' => '2137',
        ]);

        $response->assertStatus(401);
    }

    public function testItReturnsCorrectJson()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/ajax/pytlewski', [
            'search' => '21',
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonStructure([
            'name',
        ]);

        $response = $this->actingAs($user)->post('/ajax/pytlewski', [
            'search' => '',
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertExactJson([
            'name' => 'not found',
        ]);
    }
}
