@extends('base')

@section('head')
  <title inertia>{{ config('app.name') }}</title>

  @unless (app()->runningUnitTests())
    @vite('resources/css/style.css')
    @vite(['resources/js/browser.ts', "resources/js/Pages/{$page['component']}.svelte"])
  @endif

  @inertiaHead
@endsection

@section('body')
  @inertia
@endsection
