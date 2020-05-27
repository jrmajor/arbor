@extends('layouts.base')

@section('body')

    <div class="flex justify-center items-center min-h-screen py-12 px-2 sm:px-6 lg:px-8">
        @yield('content')
    </div>
    {{-- <div class="container mx-auto my-1 p-2" id="app">

        @hasSection('title-bar')
            <h1 class="mx-2 mb-1 mt-5 leading-none text-3xl font-medium">@yield('title-bar')</h1>
        @endif

        @hasSection('sidebar-menu')
            <div class="flex flex-col md:flex-row space-x-2 space-y-2">
                @hasSection('raw')
                    @yield('raw')
                @else
                    <div class="flex-grow p-4 bg-white rounded-lg shadow-lg">
                        @yield('content')
                    </div>
                @endif
                <div class="flex-shrink-0 p-1">
                    @yield('sidebar-menu')
                </div>
            </div>
        @else
            @hasSection('raw')
                @yield('raw')
            @else
                <div class="p-4 bg-white rounded-lg shadow-lg">
                    @yield('content')
                </div>
            @endif
        @endif

        <div class="m-1 px-3 pt-2 text-center text-gray-700 text-sm">
            &copy; 2018-{{ now()->year }} <a href="mailto:jeremiah.major@npng.pl">Jeremiah Major</a>
        </div>

    </div> --}}

@endsection
