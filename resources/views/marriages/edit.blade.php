@extends('layouts.app')

@section('title-bar')
    <x-marriage-title-bar :marriage="$marriage"/>
@endsection

@section('sidebar-menu')
    <x-marriage-menu active="edit" :marriage="$marriage"/>
@endsection

@section('content')
    @component('marriages.form', ['marriage' => $marriage, 'action' => 'edit']) @endcomponent
@endsection
