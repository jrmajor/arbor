<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
