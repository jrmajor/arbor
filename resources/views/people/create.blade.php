@extends('layouts.app')

@section('content')

    <h1 class="mb-3 mt-4 leading-none text-3xl font-medium">
        {{ __('people.add_a_new_person') }}
    </h1>

    <main class="p-6 bg-white rounded-lg shadow overflow-hidden">
        @include('people.form', ['person' => $person, 'action' => 'create'])
    </main>

@endsection
