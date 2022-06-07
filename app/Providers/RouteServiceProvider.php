<?php

namespace App\Providers;

use App\Models\Marriage;
use App\Models\Person;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const Home = '/people';

    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/routes.php'));
        });

        Route::bind('trashedPerson', fn ($id) => Person::onlyTrashed()->findOrFail($id));

        Route::bind('anyPerson', fn ($id) => Person::withTrashed()->findOrFail($id));

        Route::bind('trashedMarriage', fn ($id) => Marriage::onlyTrashed()->findOrFail($id));

        Route::bind('anyMarriage', fn ($id) => Marriage::withTrashed()->findOrFail($id));
    }
}
