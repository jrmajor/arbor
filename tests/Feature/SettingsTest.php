<?php

namespace Tests\Feature;

use App\Livewire\Settings;
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
        $this->withPermissions(0)->get('settings')->assertOk();
    }

    #[TestDox('it does not accept invalid email')]
    public function testInvalidEmail(): void
    {
        $this->withPermissions(0)
            ->livewire(Settings::class)
            ->set('email', '')
            ->call('saveEmail')
            ->assertHasErrors(['email' => 'required'])
            ->set('email', 'abc.de')
            ->call('saveEmail')
            ->assertHasErrors(['email' => 'email'])
            ->set('email', faker()->safeEmail())
            ->call('saveEmail')
            ->assertHasNoErrors('email');
    }

    #[TestDox('user can change email')]
    public function testChangeEmail(): void
    {
        $user = User::factory()->create();
        $newEmail = faker()->safeEmail();

        $this->actingAs($user)
            ->livewire(Settings::class)
            ->set('email', $newEmail)
            ->call('saveEmail')
            ->assertHasNoErrors('email');

        $this->assertSame($newEmail, $user->fresh()->email);
    }

    #[TestDox('it does not accept invalid email')]
    public function testInvalidPassword(): void
    {
        $this->withPermissions(0)
            ->livewire(Settings::class)
            ->set('password', '')
            ->call('savePassword')
            ->assertHasErrors(['password' => 'required'])
            ->set('password', '1234567')
            ->call('savePassword')
            ->assertHasErrors(['password' => 'min'])
            ->set('password', 'Abcd1234')
            ->call('savePassword')
            ->assertHasErrors(['password' => 'confirmed'])
            ->set('password_confirmation', 'Abcd1234')
            ->call('savePassword')
            ->assertHasNoErrors('password');
    }

    #[TestDox('user can change password')]
    public function testChangePassword(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->livewire(Settings::class)
            ->set('password', 'Abcd1234')
            ->set('password_confirmation', 'Abcd1234')
            ->call('savePassword')
            ->assertHasNoErrors('password')
            ->assertSet('password', null)
            ->assertSet('password_confirmation', null);

        $this->assertTrue(Hash::check('Abcd1234', $user->fresh()->password));
    }

    #[TestDox('it checks password when logging user out from other devices')]
    public function testInvalidLogoutPassword(): void
    {
        Event::fake();

        $this->withPermissions(0)
            ->livewire(Settings::class)
            ->set('logout_password', '')
            ->call('logoutOtherDevices')
            ->assertHasErrors('logout_password')
            ->set('logout_password', 'wrong_password')
            ->call('logoutOtherDevices')
            ->assertHasErrors('logout_password');

        Event::assertNotDispatched(OtherDeviceLogout::class);
    }

    #[TestDox('it can change logout user from other devices')]
    public function testLogoutDevices(): void
    {
        $user = User::factory()->create([
            'password' => $password = faker()->password,
        ]);

        Event::fake();

        $this->actingAs($user)
            ->livewire(Settings::class)
            ->set('logout_password', $password)
            ->call('logoutOtherDevices')
            ->assertHasNoErrors('logout_password')
            ->assertSet('logout_password', null);

        Event::assertDispatched(
            fn (OtherDeviceLogout $event) => $event->user->is($user),
        );
    }
}
