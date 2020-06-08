@extends('layouts.app')

@section('title-bar', __('people.add_a_new_person'))

@section('content')
    @component('people.form', ['person' => $person, 'action' => 'create'])
    @endcomponent
@endsection
