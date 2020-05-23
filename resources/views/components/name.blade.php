@if($person->id_wielcy || $person->id_pytlewski)
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

@if($person->canBeViewedBy(auth()->user()))
    <a href="{{ route('people.show', $person) }}" class="a">
@endif

    @if($person->dead)
        <i>
    @endif

        @if($person->canBeViewedBy(auth()->user()))
            {{ $person->name }}

            @if(! $person->last_name)
                @if($bold ?? false)<b>@endif
                    {{ $person->family_name }}
                @if($bold ?? false)</b>@endif
            @else
                @if(($bold ?? false) == 'l')<b>@endif
                    {{ $person->last_name }}
                @if(($bold ?? false) == 'l')</b>@endif
                (@if(($bold ?? false) == 'f')<b>@endif{{ $person->family_name }}@if(($bold ?? false) == 'f')</b>@endif)
            @endif

            @if($years ?? true)
                @if($person->birth_year && $person->death_year)
                    (&#8727;&#xFE0E;{{ $person->birth_year }}, &#10013;&#xFE0E;{{ $person->death_year }})
                @elseif($person->birth_year)
                    (&#8727;&#xFE0E;{{ $person->birth_year }})
                @elseif($person->death_year)
                    (&#10013;&#xFE0E;{{ $person->death_year }})
                @endif
            @endif
        @else
            <small>[{{ __('misc.hidden') }}]</small>
        @endif

    @if($person->dead)
        </i>
    @endif

@if($person->canBeViewedBy(auth()->user()))
    </a>
@endif

@if(optional(auth()->user())->canWrite())
    <a
        href="{{ route('people.edit', $person) }}"
        data-tippy-content="{{ __('people.edit_this_person') }}"
        class="a">
        <small>
            [№{{ $person->id }}]
        </small>
    </a>
@endif
