@extends('layouts.app')

@section('content')
    <h3>
        @if($person->sex == 'xx')
            &#9792;
        @elseif($person->sex == 'xy')
            &#9794;
        @endif

        @if($person->dead)
            <i>
        @endif

        {{ $person->name }}
        @if($person->last_name)
            {{ $person->last_name }} (z d. {{ $person->family_name }})
        @else
            {{ $person->family_name }}
        @endif

        @if($person->dead)
            </i>
        @endif

        <a href="{{ route('people.show', ['person' => $person->id]) }}"
            data-tippy-content="{{ __('people.return_to_person_overwiew') }}">
            <small class="text-lg">
                [№{{ $person->id }}]
            </small>
        </a>
    </h3>

    <dl class="mb-3">
        @foreach($activities as $activity)
            <dt>
                {{ __('activities.'.$activity['description']) }}
                <span class="font-normal">{{ __('activities.by') }}</span>
                {{ $activity['causer']->username }}
                <br>
                <small>{{ $activity['model']->created_at->format('Y-m-d H:i') }}</small>
            </dt>

            <dd>
                <table>
                    <x-history.td-text :activity="$activity" :attribute="'id_pytlewski'"
                        :label="__('people.pytlewski.id')"/>
                    <x-history.td-text :activity="$activity" :attribute="'id_wielcy'"
                        :label="__('people.wielcy.id')"/>
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
                </table>
            </dd>
        @endforeach
    </dl>

    <br>
    @component('components.letters')
    @endcomponent
@endsection
