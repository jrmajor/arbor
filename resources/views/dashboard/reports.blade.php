@extends('layouts.app')

@section('title', 'Reports')

@section('content')

  <div class="flex flex-col md:flex-row space-x-2 space-y-2">

    <main class="grow md:w-1/2 space-y-5">

      @if ($shouldBeDead->isNotEmpty())
        <div>
          <h2 class="mb-2 leading-none text-xl font-medium">Should be dead</h2>
          <ul class="w-full p-6 bg-white rounded-lg shadow">
            @foreach ($shouldBeDead as $person)
              <li><x-name :$person/></li>
            @endforeach
          </ul>
        </div>
      @endif

      @if ($visibleAlive->isNotEmpty())
        <div>
          <h2 class="mb-2 leading-none text-xl font-medium">Visible alive</h2>
          <ul class="w-full p-6 bg-white rounded-lg shadow">
            @foreach ($visibleAlive as $person)
              <li><x-name :$person/></li>
            @endforeach
          </ul>
        </div>
      @endif

      @if ($invisibleDead->isNotEmpty())
        <div>
          <h2 class="mb-2 leading-none text-xl font-medium">Invisible dead</h2>
          <ul class="w-full p-6 bg-white rounded-lg shadow">
            @foreach ($invisibleDead as $person)
              <li><x-name :$person/></li>
            @endforeach
          </ul>
        </div>
      @endif

    </main>

    <div class="shrink-0 p-1">
      <x-sidebar-menus.dashboard active="reports"/>
    </div>

  </div>

@endsection
