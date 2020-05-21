@php

    if (! isset($raw)) {
        $raw = false;
    }

    if (! isset($bold)) {
        $bold = false;
    }

    if (! $raw && optional(auth()->user())->canWrite()) {
        $edit = true;
    } else {
        $edit = false;
    }

@endphp

@if($person->id_wielcy || $person->id_pytlewski AND ! $raw)
    <div class="external-small-container">
        @if($person->id_wielcy)
            <a
                class="external-small-link link-wielcy-active"
                href="{{ $person->wielcy->url }}" target="_blank">
            </a>
        @else
            <a class="external-small-link link-wielcy"></a>
        @endif
        @if($person->id_pytlewski)
            <a
                class="external-small-link link-pytlewski-active"
                href="{{ $person->pytlewski->url }}" target="_blank">
            </a>
        @else
            <a class="external-small-link link-pytlewski"></a>
        @endif
    </div>
@endif

@if(! $raw && $person->canBeViewedBy(auth()->user()))
    <a href="{{ route('people.show', $person) }}" class="a">
@endif

    @if($person->dead && ! $raw)
        <i>
    @endif

        @if($person->canBeViewedBy(auth()->user()))
            {{ $person->name }}

            @if(! $person->last_name)
                @if($bold)
                    <b>
                @endif
                    {{ $person->family_name }}
                @if($bold)
                    </b>
                @endif
            @else
                @if($bold == 'l')
                    <b>
                @endif
                    {{ $person->last_name }}
                @if($bold == 'l')
                    </b>
                @endif
                (z d. @if($bold == 'f')<b>@endif{{ $person->family_name }}@if($bold == 'f')</b>@endif)
            @endif

            @if($person->birth_year && $person->death_year)
                (&#8727;&#xFE0E;{{ $person->birth_year }}, &#10013;&#xFE0E;{{ $person->death_year }})
            @elseif($person->birth_year)
                (&#8727;&#xFE0E;{{ $person->birth_year }})
            @elseif($person->death_year)
                (&#10013;&#xFE0E;{{ $person->death_year }})
            @endif
        @else
            <small>[{{ __('misc.hidden') }}]</small>
        @endif

    @if($person->dead && ! $raw)
        </i>
    @endif

@if(! $raw && $person->canBeViewedBy(auth()->user()))
    </a>
@endif

@if(! $raw && $edit)
    <a
        href="{{ route('people.edit', $person) }}"
        data-tippy-content="{{ __('people.edit_this_person') }}"
        class="a">
@endif
@if(! $raw)
    <small class="text-muted">
@endif
    [№{{ $person->id }}]
@if(! $raw)
    </small>
@endif
@if(! $raw && $edit)
    </a>
@endif
