<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        'App\Person' => 'App\Policies\PersonPolicy',
        'App\Marriage' => 'App\Policies\MarriagePolicy',
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
