@extends('layouts.base')

@section('head')
    <title inertia>{{ config('app.name') }}</title>

    @unless (app()->runningUnitTests())
      @vite('resources/css/style.css')
      @vite('resources/js/classicApp.ts')
      @vite(['resources/js/inertiaApp.ts', "resources/js/Pages/{$page['component']}.svelte"])
    @endif

    @inertiaHead
@endsection

@section('body')

  <x-menu/>

  <div class="container mx-auto my-1 p-2 pt-5">

    <x-flash/>

    @inertia

    <footer class="m-1 px-3 pt-2 text-center text-gray-600 text-sm" x-data>
      &copy; 2018-{{ now()->year }} <a x-bind:href="'mailto:' + atob('anJoLm1qckBnbWFpbC5jb20=')">Jeremiasz Major</a>
    </footer>

  </div>

@endsection
