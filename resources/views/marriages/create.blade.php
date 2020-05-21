@extends('layouts.app')

@section('title-bar')
    {{ __('marriages.add_a_new_marriage') }}
@endsection

@section('content')
    @component('marriages.form', ['marriage' => $marriage, 'action' => 'create'])
    @endcomponent
@endsection
