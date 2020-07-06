@extends('layouts.app')

@section('title', $person->formatSimpleName().' - '.__('people.titles.editing'))

@section('content')

    <h1 class="mb-3 mt-4 leading-none text-3xl font-medium">
        <x-person-title-bar :person="$person"/>
    </h1>

    <div class="flex flex-col md:flex-row space-x-2 space-y-2">

        <main class="flex-grow md:w-1/2 p-6 bg-white rounded-lg shadow overflow-hidden">
            @include('people.includes.form', ['person' => $person, 'action' => 'edit'])
        </main>

        <div class="flex-shrink-0 p-1">
            <x-person-menu active="edit" :person="$person"/>
        </div>

    </div>

@endsection
