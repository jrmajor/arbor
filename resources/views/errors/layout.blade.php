@extends('layouts.errors')

@section('title')
  {{ __('misc.titles.error') }} @yield('code')
@endsection

@section('content')
  <h1 class="text-3xl">&#10013;&#xFE0E; @yield('code') <span class="text-2xl text-gray-600">@yield('message')</span></h1>
@endsection
