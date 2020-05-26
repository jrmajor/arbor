<?php

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Testing\TestResponse;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

function actingAs(Authenticatable $user, string $driver = null): TestCase
{
    return test()->actingAs($user, $driver);
}

function withPermissions(int $permissions): TestCase
{
    return test()->withPermissions($permissions);
}

function livewire($name, $params = [])
{
    return test()->livewire($name, $params);
}

function assertGuest($guard = null)
{
    return test()->assertGuest($guard);
}

function assertAuthenticated($guard = null)
{
    return test()->assertAuthenticated($guard);
}

function assertAuthenticatedAs($user, $guard = null)
{
    return test()->assertAuthenticatedAs($user, $guard);
}

function from(string $url): TestCase
{
    return test()->from($url);
}

function get($uri, array $headers = []): TestResponse
{
    return test()->get($uri, $headers = []);
}

function post($uri, array $data = [], array $headers = []): TestResponse
{
    return test()->post($uri, $data, $headers);
}

function put($uri, array $data = [], array $headers = []): TestResponse
{
    return test()->put($uri, $data, $headers);
}

function patch($uri, array $data = [], array $headers = []): TestResponse
{
    return test()->patch($uri, $data, $headers);
}

function delete($uri, array $data = [], array $headers = []): TestResponse
{
    return test()->delete($uri, $data, $headers);
}

function options($uri, array $data = [], array $headers = []): TestResponse
{
    return test()->options($uri, $data, $headers);
}

function latestLog(): ?Activity
{
    return Activity::orderBy('id', 'desc')->first();
}

function travel($date, Closure $callback = null): Carbon
{
    if ($date == 'back') {
        Carbon::setTestNow();
        return Carbon::now();
    }

    Carbon::setTestNow($date);

    if ($callback) {
        $callback();

        Carbon::setTestNow();
    }

    return Carbon::now();
}

function assertRouteUsesFormRequest(string $routeName, string $formRequest)
{
    return test()->assertRouteUsesFormRequest($routeName, $formRequest);
}

function assertActionUsesFormRequest(string $controller, string $method, string $formRequest)
{
    return test()->assertActionUsesFormRequest($controller, $method, $formRequest);
}
