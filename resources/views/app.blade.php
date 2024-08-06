@extends('layouts.base')

@section('head')
    <title inertia>{{ config('app.name') }}</title>

    <script>
      var arborProps = {
        appName: {{ new Js(config('app.name')) }},
        currentLocale: {{ new Js(app()->getLocale()) }},
        fallbackLocale: {{ new Js(config('app.fallback_locale')) }},
        otherAvailableLocales: {{ new Js(array_values(array_diff(config('app.available_locales'), [app()->getLocale()]))) }},
      };
    </script>

    @unless (app()->runningUnitTests())
      @vite('resources/css/style.css')
      @vite('resources/js/classicApp.ts')
      @vite(['resources/js/inertiaApp.ts', "resources/js/Pages/{$page['component']}.svelte"])
    @endif

    @inertiaHead
@endsection

@section('body')
  @inertia
@endsection
