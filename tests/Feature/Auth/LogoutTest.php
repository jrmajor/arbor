<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\CurrentDeviceLogout;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class LogoutTest extends TestCase
{
    #[TestDox('it logs user out')]
    public function testLogOut(): void
    {
        $user = User::factory()->create();

        Event::fake();

        $this->actingAs($user)->post('logout');

        Event::assertDispatched(
            fn (CurrentDeviceLogout $event) => $event->user->is($user),
        );

        $this->assertGuest();
    }

    #[TestDox('it redirects to welcome page after logging out')]
    public function testRedirect(): void
    {
        $this->withPermissions(0)
            ->post('logout')
            ->assertStatus(302)
            ->assertRedirect('people');
    }

    #[TestDox('it redirects to welcome page if no user is authenticated')]
    public function testNoUser(): void
    {
        $this->post('logout')
            ->assertStatus(302)
            ->assertRedirect('people');
    }
}
