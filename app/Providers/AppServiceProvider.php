<?php

namespace App\Providers;

use App\Observers\PersonObserver;
use App\Person;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Spatie\Flash\Flash;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Person::observe(PersonObserver::class);

        Blade::directive('encodedjson', function ($expression) {
            return "<?php echo e(json_encode($expression)) ?>";
        });

        Arr::macro('trim', function ($array) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = Arr::trim($value);
                    if (blank($array[$key])) {
                        unset($array[$key]);
                    }
                } else {
                    $array[$key] = trim($value);
                    if (blank($array[$key])) {
                        unset($array[$key]);
                    }
                }
            }

            return $array;
        });

        Flash::levels([
            'success' => 'success',
            'warning' => 'warning',
            'error' => 'error',
        ]);
    }
}
