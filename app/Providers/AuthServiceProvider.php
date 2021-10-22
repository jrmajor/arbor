<?php

namespace App\Providers;

use App\Models;
use App\Policies;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Models\Person::class => Policies\PersonPolicy::class,
        Models\Marriage::class => Policies\MarriagePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
