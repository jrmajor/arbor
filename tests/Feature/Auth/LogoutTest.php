<?php

use App\Models\User;
use Illuminate\Auth\Events\CurrentDeviceLogout;
use Illuminate\Support\Facades\Event;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertGuest;

it('logs user out', function () {
    $user = User::factory()->create();

    Event::fake();

    actingAs($user)
        ->post('/logout');

    Event::assertDispatched(
        fn (CurrentDeviceLogout $event) => $event->user->is($user),
    );

    assertGuest();
});

it('redirects to welcome page after logging out')
    ->withPermissions(0)
    ->post('/logout')
    ->assertStatus(302)
    ->assertRedirect('people');

it('redirects to welcome page if no user is authenticated')
    ->post('/logout')
    ->assertStatus(302)
    ->assertRedirect('people');
