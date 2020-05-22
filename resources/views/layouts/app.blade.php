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
        @stack('scripts')
        <script src="{{ mix('/js/app.js') }}" defer></script>
        <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    </head>
    <body class="font-sans bg-gray-100">

        <x:menu/>

        <div class="container mx-auto my-1 p-1" id="app">

            @hasSection('raw')
                @yield('raw')
            @else
                @hasSection('title-bar')
                    <h1 class="mx-2 mb-1 mt-5 leading-none text-3xl font-medium">@yield('title-bar')</h1>
                @endif

                @hasSection('sidebar-menu')
                    <div class="flex flex-col md:flex-row">
                        <div class="flex-grow m-1 p-4 bg-white rounded-lg shadow-lg">
                            @yield('content')
                        </div>
                        <div class="flex-shrink-0 m-1 p-1">
                            @yield('sidebar-menu')
                        </div>
                    </div>
                @else
                    <div class="m-1 mb-2 p-4 bg-white rounded-lg shadow-lg">
                        @yield('content')
                    </div>
                @endif
            @endif
        </div>

    </body>
</html>
