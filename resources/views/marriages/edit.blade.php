@extends('layouts.app')

@section('content')

    <h1 class="mx-2 mb-1 mt-5 leading-none text-3xl font-medium">
        <x-marriage-title-bar :marriage="$marriage"/>
    </h1>

    <div class="flex flex-col md:flex-row space-x-2 space-y-2">

        <main class="flex-grow p-4 bg-white rounded-lg shadow-lg">
            @include('marriages.form', ['marriage' => $marriage, 'action' => 'edit'])
        </main>

        <div class="flex-shrink-0 p-1">
            <x-marriage-menu active="edit" :marriage="$marriage"/>
        </div>

    </div>

@endsection
