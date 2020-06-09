@extends('layouts.app')

@section('content')

    <h1 class="mx-2 mb-1 mt-5 leading-none text-3xl font-medium">
        <x-person-title-bar :person="$person"/>
    </h1>

    <div class="flex flex-col md:flex-row space-x-2 space-y-2">

        <div class="flex-grow p-4 bg-white rounded-lg shadow-lg">
            <dl>
                @foreach($activities as $activity)
                    <dt>
                        @if($activity['description'] == 'changed-visibility')
                            {{ $activity['attributes']['visibility'] ? __('activities.made_visible') : __('activities.made_invisible') }}
                        @else
                            {{ __('activities.'.$activity['description']) }}
                        @endif

                        <span class="font-normal">{{ __('activities.by') }}</span>
                        {{ optional($activity['causer'])->username }}

                        <br>
                        <small>{{ $activity['model']->created_at->format('Y-m-d H:i') }}</small>
                    </dt>

                    <dd>
                        <table>
                            <x-history.td-text :activity="$activity" :attribute="'id_pytlewski'"
                                :label="__('people.pytlewski.id')"/>
                            <x-history.td-text :activity="$activity" :attribute="'id_wielcy'"
                                :label="__('people.wielcy.id')"/>
                            <x-history.td-sex :activity="$activity"/>
                            <x-history.td-text :activity="$activity" :attribute="'name'"/>
                            <x-history.td-text :activity="$activity" :attribute="'middle_name'"/>
                            <x-history.td-text :activity="$activity" :attribute="'family_name'"/>
                            <x-history.td-text :activity="$activity" :attribute="'last_name'"/>
                            <x-history.td-date :activity="$activity" :attribute="'birth_date'"/>
                            <x-history.td-text :activity="$activity" :attribute="'birth_place'"/>
                            <x-history.td-text :activity="$activity" :attribute="'dead'"/>
                            <x-history.td-date :activity="$activity" :attribute="'death_date'"/>
                            <x-history.td-text :activity="$activity" :attribute="'death_place'"/>
                            <x-history.td-text :activity="$activity" :attribute="'death_cause'"/>
                            <x-history.td-date :activity="$activity" :attribute="'funeral_date'"/>
                            <x-history.td-text :activity="$activity" :attribute="'funeral_place'"/>
                            <x-history.td-date :activity="$activity" :attribute="'burial_date'"/>
                            <x-history.td-text :activity="$activity" :attribute="'burial_place'"/>
                            <x-history.td-text :activity="$activity" :attribute="'mother_id'"
                                :label="__('people.mother')"/>
                            <x-history.td-text :activity="$activity" :attribute="'father_id'"
                                :label="__('people.father')"/>
                            <x-history.td-sources :activity="$activity" :attribute="'sources'"/>
                        </table>
                    </dd>
                @endforeach
            </dl>
        </div>

        <div class="flex-shrink-0 p-1">
            <x-person-menu active="history" :person="$person"/>
        </div>

    </div>

@endsection
