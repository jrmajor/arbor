@extends('layouts.app')

@section('content')

    <h1 class="mx-2 mb-1 mt-5 leading-none text-3xl font-medium">
        <x-marriage-title-bar :marriage="$marriage"/>
    </h1>

    <div class="flex flex-col md:flex-row space-x-2 space-y-2">

        <main class="flex-grow flex flex-col space-y-3">

            @foreach($activities as $activity)

                <div class="p-4 bg-white rounded-lg shadow-lg overflow-hidden">
                    <table class="block md:table">
                        <x-history.td-text :activity="$activity" :attribute="'woman_id'"
                            :label="__('marriages.woman')"/>
                        <x-history.td-text :activity="$activity" :attribute="'woman_order'"/>

                        <x-history.td-text :activity="$activity" :attribute="'man_id'"
                            :label="__('marriages.man')"/>
                        <x-history.td-text :activity="$activity" :attribute="'man_order'"/>

                        <x-history.td-enum :activity="$activity" :attribute="'rite'"
                            :translations="'marriages.rites'"/>

                        <x-history.td-enum :activity="$activity" :attribute="'first_event_type'"
                            :translations="'marriages.event_types'"/>
                        <x-history.td-date :activity="$activity" :attribute="'first_event_date'"/>
                        <x-history.td-text :activity="$activity" :attribute="'first_event_place'"/>
                        <x-history.td-enum :activity="$activity" :attribute="'second_event_type'"
                            :translations="'marriages.event_types'"/>
                        <x-history.td-date :activity="$activity" :attribute="'second_event_date'"/>
                        <x-history.td-text :activity="$activity" :attribute="'second_event_place'"/>

                        <x-history.td-text :activity="$activity" :attribute="'ended'"/>
                        <x-history.td-text :activity="$activity" :attribute="'end_cause'"/>
                        <x-history.td-date :activity="$activity" :attribute="'end_date'"/>

                        <x-history.td-text :activity="$activity" :attribute="'divorced'"/>
                        <x-history.td-date :activity="$activity" :attribute="'divorce_date'"/>
                        <x-history.td-text :activity="$activity" :attribute="'divorce_place'"/>
                    </table>

                    <div class="-m-4 mt-4 px-4 py-3 bg-gray-100 flex items-center justify-between">
                        {{ optional($activity['causer'])->username }}
                        <small>{{ $activity['model']->created_at->format('Y-m-d H:i') }}</small>
                    </div>
                </div>

            @endforeach

        </main>

        <div class="flex-shrink-0 p-1">
            <x-marriage-menu active="history" :marriage="$marriage"/>
        </div>

    </div>

@endsection
