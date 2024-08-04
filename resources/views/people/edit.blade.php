@extends('layouts.app-classic')

@section('title', $person->formatSimpleName().' - '.__('people.titles.editing'))

@section('content')

  <h1 class="mb-3 leading-none text-3xl font-medium">
    <x-person-title-bar :$person/>
  </h1>

  <div class="flex flex-col md:flex-row space-x-2 space-y-2">

    <main class="grow md:w-1/2 p-6 bg-white rounded-lg shadow overflow-hidden">
      @include('people.includes.form', ['person' => $person, 'action' => 'edit'])
    </main>

    <div class="shrink-0 p-1">
      <x-sidebar-menus.person activePage="edit" :$person/>
    </div>

  </div>

@endsection
