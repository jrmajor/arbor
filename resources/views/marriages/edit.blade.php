@extends('layouts.app')

@section('title', __('marriages.titles.editing_marriage'))

@section('content')

  <h1 class="mb-3 leading-none text-3xl font-medium">
    <x-marriage-title-bar :$marriage/>
  </h1>

  <div class="flex flex-col md:flex-row space-x-2 space-y-2">

    <main class="grow md:w-1/2 p-6 bg-white rounded-lg shadow overflow-hidden">
      @include('marriages.form', ['marriage' => $marriage, 'action' => 'edit'])
    </main>

    <div class="shrink-0 p-1">
      <x-sidebar-menus.marriage activePage="edit" :$marriage/>
    </div>

  </div>

@endsection
