<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="font-size: .9em">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @hasSection('title')
      <title>@yield('title') - {{ config('app.name') }}</title>
    @else
      <title>{{ config('app.name') }}</title>
    @endif

    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

    @routes

    @unless (app()->runningUnitTests())
      @vite(['resources/css/style.css', 'resources/js/app.ts'])
    @endif

    @livewireScriptConfig

    @if (config('services.fathom.id'))
      <script src="https://cdn.usefathom.com/script.js" data-site="{{ config('services.fathom.id') }}" defer></script>
    @endif
  </head>
  <body class="font-sans bg-gray-100">

    @yield('body')

  </body>
</html>
