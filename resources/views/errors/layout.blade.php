@extends('base')

@section('head')
  <title>{{ __('misc.titles.error') }} @yield('code') - {{ config('app.name') }}</title>

  @unless (app()->runningUnitTests())
    @vite('resources/css/style.css')
  @endif
@endsection

@section('body')
  <div class="flex justify-center items-center min-h-screen py-12 px-2 sm:px-6 lg:px-8">
    <h1 class="text-3xl">
      &#10013;&#xFE0E;
      @yield('code')
      <span class="text-2xl text-gray-600">@yield('message')</span>
    </h1>
  </div>
@endsection
