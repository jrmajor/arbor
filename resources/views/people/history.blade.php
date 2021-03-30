@extends('layouts.app')

@section('title', $person->formatSimpleName().' - '.__('people.titles.edits_history'))

@section('content')

  <h1 class="mb-3 leading-none text-3xl font-medium">
    <x-person-title-bar :person="$person"/>
  </h1>

  <div class="flex flex-col md:flex-row space-x-2 space-y-2">

    <main class="flex-grow md:w-1/2 flex flex-col space-y-3">

      @foreach($activities as $activity)

        <div class="p-6 bg-white rounded-lg shadow overflow-hidden">
          <table class="block md:table">
            <tbody class="block md:table-row-group">
              @if($activity['description'] === 'deleted')
                {{ __('people.history.deleted') }}
              @elseif($activity['description'] === 'restored')
                {{ __('people.history.restored') }}
              @elseif($activity['description'] === 'changed-visibility')
                {{ $activity['attributes']['visibility'] ? __('people.history.made_visible') : __('people.history.made_invisible') }}
              @elseif($activity['description'] === 'added-biography')
                {{ __('people.history.added-biography') }}

                <div class="mt-3 p-4 bg-gray-50 text-gray-700 rounded-md space-y-2 break-words">
                  {!! Str::formatBiography($activity['new']) !!}
                </div>
              @elseif($activity['description'] === 'updated-biography')
                {{ __('people.history.updated-biography') }}

                <div class="mt-3 p-4 bg-gray-50 text-gray-700 rounded-md space-y-2 break-words">
                  {!! Str::formatBiography($activity['new']) !!}
                </div>
              @elseif($activity['description'] === 'deleted-biography')
                {{ __('people.history.deleted-biography') }}
              @else
                <x-history.text :activity="$activity" :attribute="'id_pytlewski'"
                  :label="__('people.pytlewski.id')"/>
                <x-history.text :activity="$activity" :attribute="'id_wielcy'"
                  :label="__('people.wielcy.id')"/>
                <x-history.sex :activity="$activity"/>
                <x-history.text :activity="$activity" :attribute="'name'"/>
                <x-history.text :activity="$activity" :attribute="'middle_name'"/>
                <x-history.text :activity="$activity" :attribute="'family_name'"/>
                <x-history.text :activity="$activity" :attribute="'last_name'"/>
                <x-history.date :activity="$activity" :attribute="'birth_date'"/>
                <x-history.text :activity="$activity" :attribute="'birth_place'"/>
                <x-history.text :activity="$activity" :attribute="'dead'"/>
                <x-history.date :activity="$activity" :attribute="'death_date'"/>
                <x-history.text :activity="$activity" :attribute="'death_place'"/>
                <x-history.text :activity="$activity" :attribute="'death_cause'"/>
                <x-history.date :activity="$activity" :attribute="'funeral_date'"/>
                <x-history.text :activity="$activity" :attribute="'funeral_place'"/>
                <x-history.date :activity="$activity" :attribute="'burial_date'"/>
                <x-history.text :activity="$activity" :attribute="'burial_place'"/>
                <x-history.text :activity="$activity" :attribute="'mother_id'"
                  :label="__('people.mother')"/>
                <x-history.text :activity="$activity" :attribute="'father_id'"
                  :label="__('people.father')"/>
                <x-history.sources :activity="$activity" :attribute="'sources'"/>
              @endif
            </tbody>
          </table>

          <div class="-m-6 mt-5 px-6 py-4 bg-gray-50 flex items-center justify-between">
            {{ $activity['causer']?->username }}
            <small>{{ $activity['model']->created_at->format('Y-m-d H:i') }}</small>
          </div>
        </div>

      @endforeach

    </main>

    <div class="flex-shrink-0 p-1">
      <x-sidebar-menus.person active="history" :person="$person"/>
    </div>

  </div>

@endsection
