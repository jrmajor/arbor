@extends('layouts.app')

@section('title-bar')
    {{ __('marriages.edit_this_marriage') }}
    <small class="text-lg">[№{{ $marriage->id }}]</small>
@endsection

@section('sidebar-menu')
    <x-marriage-menu active="edit" :marriage="$marriage"/>
@endsection

@section('content')
    @component('marriages.form', ['marriage' => $marriage, 'action' => 'edit']) @endcomponent
@endsection
