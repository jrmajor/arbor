@extends('layouts.app')

@section('content')
    <h3>{{ __('people.add_a_new_person') }}</h3>

    @component('people.form', ['person' => $person, 'action' => 'create'])
    @endcomponent
@endsection
