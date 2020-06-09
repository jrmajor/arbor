@extends('layouts.app')

@section('content')

    <h1 class="mx-2 mb-1 mt-5 leading-none text-3xl font-medium">
        {{ __('people.add_a_new_person') }}
    </h1>

    <div class="p-4 bg-white rounded-lg shadow-lg">
        @include('people.form', ['person' => $person, 'action' => 'create'])
    </div>

@endsection
