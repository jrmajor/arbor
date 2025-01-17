<?php

namespace App\Providers;

use App\Http\InertiaHttpGateway;
use App\Models;
use App\Models\Observers\PersonObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Ssr\Gateway;
use Spatie\Flash\Flash;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(Gateway::class, InertiaHttpGateway::class);

        JsonResource::withoutWrapping();
        ResourceCollection::withoutWrapping();

        $this->registerModelsConfig();
        $this->registerRouteBindings();

        Flash::levels([
            'success' => 'success',
            'warning' => 'warning',
            'error' => 'error',
        ]);
    }

    private function registerModelsConfig(): void
    {
        Models\Person::observe(PersonObserver::class);

        Relation::enforceMorphMap([
            'marriage' => Models\Marriage::class,
            'person' => Models\Person::class,
            'user' => Models\User::class,
        ]);

        $shouldBeStrict = ! $this->app->environment('production');

        Model::preventLazyLoading($shouldBeStrict);
        // Model::preventSilentlyDiscardingAttributes($shouldBeStrict);
        Model::preventAccessingMissingAttributes($shouldBeStrict);
    }

    private function registerRouteBindings(): void
    {
        Route::bind('trashedPerson', fn ($id) => Models\Person::onlyTrashed()->findOrFail($id));
        Route::bind('anyPerson', fn ($id) => Models\Person::withTrashed()->findOrFail($id));

        Route::bind('trashedMarriage', fn ($id) => Models\Marriage::onlyTrashed()->findOrFail($id));
        Route::bind('anyMarriage', fn ($id) => Models\Marriage::withTrashed()->findOrFail($id));
    }
}
