@extends('layouts.app')

@section('content')
    <h3>
        {{ __('people.edits_history') }}

        <a href="{{ route('marriages.edit', ['marriage' => $marriage->id]) }}"
            data-tippy-content="{{ __('marriages.return_to_marriage_edition') }}">
            <small class="text-lg">
                [№{{ $marriage->id }}]
            </small>
        </a>
    </h3>

    <dl class="mb-3">
        @foreach($activities as $activity)
            <dt>
                {{ __('activities.'.$activity['description']) }}
                <span class="font-normal">{{ __('activities.by') }}</span>
                {{ optional($activity['causer'])->username }}
                <br>
                <small>{{ $activity['model']->created_at->format('Y-m-d H:i') }}</small>
            </dt>

            <dd>
                <table>
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
                </table>
            </dd>
        @endforeach
    </dl>

    <br>
    @component('components.letters')
    @endcomponent
@endsection
