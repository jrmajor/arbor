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
            <small class="text-lg">[№{{ $person->id }}]</small>
        </a>
        <a
            href="{{ route('people.destroy', ['person' => $person->id]) }}"
            onclick="event.preventDefault();document.getElementById('delete-person-form').submit();">
            <small class="text-lg text-red-500">[{{ __('people.delete') }}]</small>
        </a>
        <a href="{{ route('marriages.create') }}">
            <small class="text-lg">[{{ strtolower(__('marriages.add_a_new_marriage')) }}]</small>
        </a>
        @if(optional(auth()->user())->isSuperAdmin())
            <a href="{{ route('people.history', ['maybe_trashed_person' => $person->id]) }}">
                <small class="text-lg">
                    [{{ strtolower(__('people.edits_history')) }}]
                </small>
            </a>
            <a
                href="{{ route('people.changeVisibility', ['person' => $person->id]) }}"
                onclick="event.preventDefault();document.getElementById('change-visibility-form').submit();">
                <small class="text-lg text-red-500">
                    [{{ $person->isVisible() ? __('people.make_invisible') : __('people.make_visible') }}]
                </small>
            </a>
            <form id="change-visibility-form" method="POST" style="display: none"
            action="{{ route('people.changeVisibility', ['person' => $person->id]) }}">
                @method('PUT')
                @csrf
                <input type="hidden" name="visibility" value="{{ $person->isVisible() ? '0' : '1' }}">
            </form>
        @endif
    </h3>

    <form id="delete-person-form" method="POST" style="display: none"
    action="{{ route('people.destroy', ['person' => $person->id]) }}">
        @method('DELETE')
        @csrf
    </form>

    @error('deleting')
        <p class="text-red-500">{{ $message }}</p>
    @enderror

    @component('people.form', ['person' => $person, 'action' => 'edit']) @endcomponent
@endsection
