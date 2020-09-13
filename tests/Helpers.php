<?php

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Testing\TestResponse;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

function withPermissions(int $permissions): TestCase
{
    return test()->withPermissions($permissions);
}

function latestLog(): ?Activity
{
    return Activity::orderBy('id', 'desc')->first();
}

function assertRouteUsesFormRequest(string $routeName, string $formRequest)
{
    return test()->assertRouteUsesFormRequest($routeName, $formRequest);
}

function assertActionUsesFormRequest(string $controller, string $method, string $formRequest)
{
    return test()->assertActionUsesFormRequest($controller, $method, $formRequest);
}
