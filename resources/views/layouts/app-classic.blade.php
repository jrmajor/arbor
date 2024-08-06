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

    @livewireStyles
@endsection

@section('body')

  <x-menu/>

  <div class="container mx-auto my-1 p-2 pt-5">

    <x-flash/>

    @yield('content')

    <footer class="m-1 px-3 pt-2 text-center text-gray-600 text-sm" x-data>
      &copy; 2018-{{ now()->year }} <a x-bind:href="'mailto:' + atob('anJoLm1qckBnbWFpbC5jb20=')">Jeremiasz Major</a>
    </footer>

  </div>

@endsection
