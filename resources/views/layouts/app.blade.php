<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="font-size: .85em">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Arbor') }}</title>
        <script src="{{ mix('/js/app.js') }}" defer></script>
        <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    </head>
    <body class="font-body">
        <div class="container mx-auto my-5 p-4" id="app">
            <x:menu/>

            <div class="p-1 sm:p-2">
                @yield('content')
            </div>
        </div>
    </body>
</html>
