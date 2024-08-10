<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\OtherDeviceLogout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class SettingsTest extends TestCase
{
    #[TestDox('guest is asked to log in when attempting to view settings page')]
    public function testAuthGuest(): void
    {
        $this->get('settings')->assertFound()->assertRedirect('login');
    }

    #[TestDox('user can view settings page')]
    public function testAuthUser(): void
    {
        $this->withPermissions(0)->get('settings')->assertInertiaOk([], 'Settings');
    }

    #[TestDox('user can change email')]
    public function testChangeEmail(): void
    {
        $user = User::factory()->create();
        $newEmail = faker()->safeEmail();

        $this->actingAs($user)
            ->put('settings/email', ['email' => $newEmail])
            ->assertOk();

        $this->assertSame($newEmail, $user->fresh()->email);
    }

    #[TestDox('user can change password')]
    public function testChangePassword(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('settings/password', [
            'password' => 'Abcd1234',
            'password_confirmation' => 'Abcd1234',
        ])->assertOk();

        $this->assertTrue(Hash::check('Abcd1234', $user->fresh()->password));
    }

    #[TestDox('it can change logout user from other devices')]
    public function testLogoutDevices(): void
    {
        $user = User::factory()->create([
            'password' => $password = faker()->password,
        ]);

        Event::fake();

        $this->actingAs($user)->post(
            'settings/logout-other-devices',
            ['password' => $password],
        )->assertRedirect();

        Event::assertDispatched(
            fn (OtherDeviceLogout $event) => $event->user->is($user),
        );
    }
}
