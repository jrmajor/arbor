@extends('layouts.base')

@section('body')

    <x-menu/>

    <div class="container mx-auto my-1 p-2">

        <x-flash/>

        @yield('content')

        <footer class="m-1 px-3 pt-2 text-center text-gray-700 text-sm">
            &copy; 2018-{{ now()->year }} <a href="mailto:jeremiah.major@npng.pl">Jeremiah Major</a>
        </footer>

    </div>

@endsection
