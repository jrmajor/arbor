<?php

namespace Tests;

use App\Models\Activity;

function withPermissions(int $permissions): TestCase
{
    return test()->withPermissions($permissions);
}

function latestLog(): ?Activity
{
    return Activity::orderByDesc('id')->first();
}
