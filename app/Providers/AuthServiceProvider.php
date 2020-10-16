<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        'App\Models\Person' => 'App\Policies\PersonPolicy',
        'App\Models\Marriage' => 'App\Policies\MarriagePolicy',
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
