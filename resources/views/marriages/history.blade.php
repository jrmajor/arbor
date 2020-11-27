@extends('layouts.app')

@section('title', __('marriages.titles.marriage_edits_history'))

@section('content')

    <h1 class="mb-3 leading-none text-3xl font-medium">
        <x-marriage-title-bar :marriage="$marriage"/>
    </h1>

    <div class="flex flex-col md:flex-row space-x-2 space-y-2">

        <main class="flex-grow md:w-1/2 flex flex-col space-y-3">

            @foreach($activities as $activity)

                <div class="p-6 bg-white rounded-lg shadow overflow-hidden">
                    <table class="block md:table">
                        <tbody class="block md:table-row-group">
                            @if($activity['description'] === 'deleted')
                                {{ __('marriages.history.deleted') }}
                            @elseif($activity['description'] === 'restored')
                                {{ __('marriages.history.restored') }}
                            @else
                                <x-history.text :activity="$activity" :attribute="'woman_id'"
                                    :label="__('marriages.woman')"/>
                                <x-history.text :activity="$activity" :attribute="'woman_order'"/>

                                <x-history.text :activity="$activity" :attribute="'man_id'"
                                    :label="__('marriages.man')"/>
                                <x-history.text :activity="$activity" :attribute="'man_order'"/>

                                <x-history.enum :activity="$activity" :attribute="'rite'"
                                    :translations="'marriages.rites'"/>

                                <x-history.enum :activity="$activity" :attribute="'first_event_type'"
                                    :translations="'marriages.event_types'"/>
                                <x-history.date :activity="$activity" :attribute="'first_event_date'"/>
                                <x-history.text :activity="$activity" :attribute="'first_event_place'"/>
                                <x-history.enum :activity="$activity" :attribute="'second_event_type'"
                                    :translations="'marriages.event_types'"/>
                                <x-history.date :activity="$activity" :attribute="'second_event_date'"/>
                                <x-history.text :activity="$activity" :attribute="'second_event_place'"/>

                                <x-history.text :activity="$activity" :attribute="'divorced'"/>
                                <x-history.date :activity="$activity" :attribute="'divorce_date'"/>
                                <x-history.text :activity="$activity" :attribute="'divorce_place'"/>
                            @endif
                        </tbody>
                    </table>

                    <div class="-m-6 mt-5 px-6 py-4 bg-gray-50 flex items-center justify-between">
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
