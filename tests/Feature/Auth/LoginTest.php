<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class LoginTest extends TestCase
{
    #[TestDox('authenticated users are redirected when trying to access form')]
    public function testUserForm(): void
    {
        $this->withPermissions(0)
            ->get('/login')
            ->assertStatus(302)
            ->assertRedirect('/people');

        $this->assertAuthenticated();
    }

    #[TestDox('authenticated users are redirected when trying to log in')]
    public function testUser(): void
    {
        $this->withPermissions(0)
            ->post('/login')
            ->assertStatus(302)
            ->assertRedirect('/people');

        $this->assertAuthenticated();
    }

    #[TestDox('it requires username')]
    public function testRequireUsername(): void
    {
        $this->from('/login')
            ->post('/login', [
                'password' => 'password',
            ])
            ->assertSessionHasErrors('username')
            ->assertStatus(302)
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    #[TestDox('it requires password')]
    public function testRequirePassword(): void
    {
        $this->from('/login')
            ->post('/login', [
                'username' => 'gracjan',
            ])
            ->assertSessionHasErrors('password')
            ->assertStatus(302)
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    #[TestDox('it checks if user exists')]
    public function testCheckUsername(): void
    {
        $this->from('/login')
            ->post('/login', [
                'username' => 'gracjan',
                'password' => 'hasło',
            ])
            ->assertSessionHasErrors('username')
            ->assertStatus(302)
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    #[TestDox('it checks password')]
    public function testCheckPassword(): void
    {
        User::factory()->create([
            'username' => 'gracjan',
        ]);

        $this->from('/login')
            ->post('/login', [
                'username' => 'gracjan',
                'password' => 'wrong',
            ])
            ->assertSessionHasErrors('username')
            ->assertStatus(302)
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    #[TestDox('user can log in using username')]
    public function testLogInUsername(): void
    {
        $user = User::factory()->create([
            'username' => 'gracjan',
            'password' => Hash::make($password = 'secret'),
        ]);

        Event::fake();

        $this->post('/login', [
            'username' => 'gracjan',
            'password' => 'secret',
        ])
            ->assertSessionHasNoErrors()
            ->assertStatus(302)
            ->assertRedirect('/people');

        $this->assertAuthenticatedAs($user);

        Event::assertDispatched(
            fn (Login $event) => $event->user->is($user),
        );
    }

    #[TestDox('user can log in using email')]
    public function testLogInEmail(): void
    {
        $user = User::factory()->create([
            'email' => 'gracjan@example.com',
            'password' => Hash::make($password = 'secret'),
        ]);

        Event::fake();

        $this->post('/login', [
            'username' => 'gracjan@example.com',
            'password' => 'secret',
        ])
            ->assertSessionHasNoErrors()
            ->assertStatus(302)
            ->assertRedirect('/people');

        $this->assertAuthenticatedAs($user);

        Event::assertDispatched(
            fn (Login $event) => $event->user->is($user),
        );
    }
}
