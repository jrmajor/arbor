@extends('layouts.app')

@section('content')

    <h1 class="mx-2 mb-1 mt-5 leading-none text-3xl font-medium">
        <x-person-title-bar :person="$person"/>
    </h1>

    <div class="flex flex-col md:flex-row space-x-2 space-y-2">

        <main class="flex-grow p-4 bg-white rounded-lg shadow-lg">
            @include('people.form', ['person' => $person, 'action' => 'edit'])
        </main>

        <div class="flex-shrink-0 p-1">
            <x-person-menu active="edit" :person="$person"/>
        </div>

    </div>

@endsection
