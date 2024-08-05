@extends('layouts.base')

@section('head')
    <title inertia>{{ config('app.name') }}</title>

    <script>var userLanguage = {{ new Js(app()->getLocale()) }};</script>

    @unless (app()->runningUnitTests())
      @vite('resources/css/style.css')
      @vite('resources/js/classicApp.ts')
      @vite(['resources/js/inertiaApp.ts', "resources/js/Pages/{$page['component']}.svelte"])
    @endif

    @inertiaHead
@endsection

@section('body')

  <x-menu/>

  @inertia

@endsection
