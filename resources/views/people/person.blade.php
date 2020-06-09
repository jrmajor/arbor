@extends('layouts.app')

@section('title', $person->formatSimpleName())

@section('content')

    <h1 class="mx-2 mb-1 mt-5 leading-none text-3xl font-medium">
        <x-person-title-bar :person="$person"/>
    </h1>

    <div class="flex flex-col md:flex-row space-x-2 space-y-2">

        <div class="space-y-2">
            @include('people.details')
            @include('people.small-tree')
        </div>

        @if(optional(auth()->user())->canWrite())
            <div class="flex-shrink-0 p-1">
                <x-person-menu active="show" :person="$person"/>
            </div>
        @endif

    </div>

@endsection
