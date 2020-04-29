<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Arr::macro('trim', function ($array) {
            foreach($array as $key => $value) {
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
    }
}
