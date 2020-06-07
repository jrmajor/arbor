@extends('layouts.app')

@section('title-bar')
    <x-marriage-title-bar :marriage="$marriage"/>
@endsection

@section('sidebar-menu')
    <x-marriage-menu active="history" :marriage="$marriage"/>
@endsection

@section('content')
    <dl>
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

                    <x-history.td-text :activity="$activity" :attribute="'divorced'"/>
                    <x-history.td-date :activity="$activity" :attribute="'divorce_date'"/>
                    <x-history.td-text :activity="$activity" :attribute="'divorce_place'"/>
                </table>
            </dd>
        @endforeach
    </dl>
@endsection
