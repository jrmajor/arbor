@extends('layouts.app')

@section('title-bar')
    <x-person-title-bar :person="$person"/>
@endsection

@section('sidebar-menu')
    <x-person-menu active="edit" :person="$person"/>
@endsection

@section('content')
    @error('deleting')
        <p class="text-red-500">{{ $message }}</p>
    @enderror

    @component('people.form', ['person' => $person, 'action' => 'edit']) @endcomponent
@endsection
