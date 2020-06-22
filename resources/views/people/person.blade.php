@extends('layouts.app')

@section('title', $person->formatSimpleName())

@section('content')

    <h1 class="mb-3 mt-4 leading-none text-3xl font-medium">
        <x-person-title-bar :person="$person"/>
    </h1>

    <div class="flex flex-col md:flex-row space-x-2 space-y-2">

        <main class="space-y-3">
            @include('people.details')
            @include('people.small-tree')
        </main>

        @if(optional(auth()->user())->canWrite())
            <div class="flex-shrink-0 p-1">
                <x-person-menu active="show" :person="$person"/>
            </div>
        @endif

    </div>

@endsection
