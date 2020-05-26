<?php

use App\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;

it('logs user out', function () {
    $user = factory(User::class)->create();

    Event::fake();

    actingAs($user)
        ->post('/logout');

    Event::assertDispatched(fn (Logout $event) => $event->user->is($user));
});

it('redirects to welcome page after logging out')
    ->withPermissions(0)
    ->post('/logout')
    ->assertStatus(302)
    ->assertRedirect('/');

it('redirects to welcome page if no user is authenticated')
    ->post('/logout')
    ->assertStatus(302)
    ->assertRedirect('/');
