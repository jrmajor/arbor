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

        <a href="{{ route('people.show', ['person' => $person->id]) }}">
            <small class="text-lg">[№{{ $person->id }}]</small>
        </a>
        <a href="{{ route('marriages.create') }}">
            <small class="text-lg">[{{ strtolower(__('marriages.add_a_new_marriage')) }}]</small>
        </a>
    </h3>

    @component('people.form', ['person' => $person, 'action' => 'edit']) @endcomponent
@endsection
