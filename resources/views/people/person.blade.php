@extends('layouts.app')

@section('title', $person->formatSimpleName())

@section('content')

  <h1 class="mb-3 leading-none text-3xl font-medium">
    <x-person-title-bar :$person/>
  </h1>

  <div class="flex flex-col md:flex-row space-x-2 space-y-2">

    <main class="grow md:w-1/2 flex flex-col space-y-3">
      @include('people.includes.details')
      @include('people.includes.biography')
      @include('people.includes.small-tree')
    </main>

    @canany(['update', 'changeVisibility', 'delete', 'restore', 'viewHistory'], $person)
      <div class="shrink-0 p-1">
        <x-sidebar-menus.person active="show" :$person/>
      </div>
    @endcanany

  </div>

@endsection
