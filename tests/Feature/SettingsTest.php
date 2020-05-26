<?php

use App\Http\Livewire\Settings;
use App\User;
use Illuminate\Auth\Events\OtherDeviceLogout;

test('guest are asked to log in when attempting to view settings page')
    ->get('settings')
    ->assertStatus(302)
    ->assertRedirect('login');

test('users with permissions can view settings page')
    ->withPermissions(0)
    ->get('settings')
    ->assertStatus(200);

it('doesnt accept invalid email', function () {
    withPermissions(0)
        ->livewire(Settings::class)

    ->set('email', null)
        ->call('saveEmail')
        ->assertHasErrors([
            'email' => 'required',
        ])

    ->set('email', 'abc.de')
        ->call('saveEmail')
        ->assertHasErrors([
            'email' => 'email',
        ])

    ->set('email', faker()->safeEmail)
        ->call('saveEmail')
        ->assertHasNoErrors('email');
});

it('can change email', function () {
    $user = factory(User::class)->create();
    $newEmail = faker()->safeEmail;

    actingAs($user)
        ->livewire(Settings::class)
        ->set('email', $newEmail)
        ->call('saveEmail')
        ->assertHasNoErrors('email');

    assertEquals($newEmail, $user->fresh()->email);
});

it('doesnt accept invalid password', function () {
    withPermissions(0)
        ->livewire(Settings::class)

    ->set('password', null)
        ->call('savePassword')
        ->assertHasErrors([
            'password' => 'required',
        ])

    ->set('password', '1234567')
        ->call('savePassword')
        ->assertHasErrors([
            'password' => 'min',
        ])

    ->set('password', 'Abcd1234')
        ->call('savePassword')
        ->assertHasErrors([
            'password' => 'confirmed',
        ])

    ->set('password_confirmation', 'Abcd1234')
        ->call('savePassword')
        ->assertHasNoErrors('password');
});

it('can change password', function () {
    $user = factory(User::class)->create();
    $newPassword = faker()->password;

    actingAs($user)
        ->livewire(Settings::class)
        ->set('password', $newPassword)
        ->set('password_confirmation', $newPassword)
        ->call('savePassword')
        ->assertHasNoErrors('password')
        ->assertSet('password', null)
        ->assertSet('password_confirmation', null);

    assertTrue(Hash::check($newPassword, $user->fresh()->password));
});

it('checks password when logging user out from other devices', function () {
    Event::fake();

    withPermissions(0)
        ->livewire(Settings::class)

    ->set('logout_password', null)
        ->call('logoutOtherDevices')
        ->assertHasErrors('logout_password')

    ->set('logout_password', 'wrong_password')
        ->call('logoutOtherDevices')
        ->assertHasErrors('logout_password');

    Event::assertNotDispatched(OtherDeviceLogout::class);
});

it('can change logout user from other devices', function () {
    $user = factory(User::class)->create([
        'password' => Hash::make($password = faker()->password),
    ]);

    Event::fake();

    actingAs($user)
        ->livewire(Settings::class)
        ->set('logout_password', $password)
        ->call('logoutOtherDevices')
        ->assertHasNoErrors('logout_password')
        ->assertSet('logout_password', null);

    Event::assertDispatched(fn (OtherDeviceLogout $event) => $event->user->is($user));
});
