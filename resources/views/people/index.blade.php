@extends('layouts.app')

@section('title', __('people.titles.index'))

@section('content')

  <main class="p-6 bg-white rounded-lg shadow">

    <x-letters
      :activeLetter="$activeLetter ?? null"
      :activeType="$activeType ?? null"
    />

    <hr class="-mx-6 my-5 border-t-2 border-dashed">

    @if (isset($list))
      <ul>
        @foreach ($list as $person)
          <li>
            <x-name :$person :bold="isset($active) ? $active['type'] : null"/>
          </li>
        @endforeach
      </ul>
    @else
      <div class="text-center">
        <small>
          {{ __('people.index.total') }}: <strong>{{ App\Models\Person::count() }}</strong>
        </small>
      </div>
    @endif

  </main>

@endsection
