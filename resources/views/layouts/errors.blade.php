@extends('layouts.base')

@section('head')
    @hasSection('title')
      <title>@yield('title') - {{ config('app.name') }}</title>
    @else
      <title>{{ config('app.name') }}</title>
    @endif

    @unless (app()->runningUnitTests())
      @vite(['resources/css/style.css', 'resources/js/classicApp.ts'])
    @endif
@endsection

@section('body')

  <div class="flex justify-center items-center min-h-screen py-12 px-2 sm:px-6 lg:px-8">
    @yield('content')
  </div>

@endsection
