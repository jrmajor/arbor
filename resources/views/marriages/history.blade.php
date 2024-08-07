@extends('layouts.app-classic')

@section('title', __('marriages.titles.marriage_edits_history'))

@section('content')

  <h1 class="mb-3 leading-none text-3xl font-medium">
    <x-marriage-title-bar :$marriage/>
  </h1>

  <div class="flex flex-col md:flex-row space-x-2 space-y-2">

    <main class="grow md:w-1/2 flex flex-col space-y-3">

      @foreach ($activities as $activity)

        <div class="p-6 bg-white rounded-lg shadow overflow-hidden">
          <table class="block md:table">
            <tbody class="block md:table-row-group">
              @if ($activity['description'] === 'deleted')
                {{ __('marriages.history.deleted') }}
              @elseif ($activity['description'] === 'restored')
                {{ __('marriages.history.restored') }}
              @else
                <x-history.text :$activity :attribute="'woman_id'"
                  :label="__('marriages.woman')"/>
                <x-history.text :$activity :attribute="'woman_order'"/>

                <x-history.text :$activity :attribute="'man_id'"
                  :label="__('marriages.man')"/>
                <x-history.text :$activity :attribute="'man_order'"/>

                <x-history.enum :$activity :attribute="'rite'"
                  :translations="'marriages.rites'"/>

                <x-history.enum :$activity :attribute="'first_event_type'"
                  :translations="'marriages.event_types'"/>
                <x-history.date :$activity :attribute="'first_event_date'"/>
                <x-history.text :$activity :attribute="'first_event_place'"/>
                <x-history.enum :$activity :attribute="'second_event_type'"
                  :translations="'marriages.event_types'"/>
                <x-history.date :$activity :attribute="'second_event_date'"/>
                <x-history.text :$activity :attribute="'second_event_place'"/>

                <x-history.text :$activity :attribute="'divorced'"/>
                <x-history.date :$activity :attribute="'divorce_date'"/>
                <x-history.text :$activity :attribute="'divorce_place'"/>
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

    <div class="shrink-0 p-1">
      <x-sidebar-menus.marriage activePage="history" :$marriage/>
    </div>

  </div>

@endsection
