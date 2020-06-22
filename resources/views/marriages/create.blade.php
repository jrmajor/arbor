@extends('layouts.app')

@section('content')

    <h1 class="mb-3 mt-4 leading-none text-3xl font-medium">
        {{ __('marriages.add_a_new_marriage') }}
    </h1>

    <main class="p-6 bg-white rounded-lg shadow overflow-hidden">
        @include('marriages.form', ['marriage' => $marriage, 'action' => 'create'])
    </main>

@endsection
