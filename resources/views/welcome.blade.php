<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Arbor') }}</title>
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <div class="flex justify-center items-center" style="height: 100vh">
            <div class="text-center">
                <h1 style="font-family: Nunito; font-size: 8rem; letter-spacing: 0.2em">arbor</h1>
                <p class="uppercase" style="letter-spacing: 0.4em">
                    <a href="{{ route('login') }}">{{ __('welcome.login') }}</a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="{{ route('people.index') }}">{{ __('welcome.guest') }}</a>
                </p>
            </div>
        </div>
    </body>
</html>
