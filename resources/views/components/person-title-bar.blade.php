@if($person->sex == 'xx')
    &#9792;&#xFE0E;
@elseif($person->sex == 'xy')
    &#9794;&#xFE0E;
@endif

@if($person->dead)
    <i>
@endif

    @if($person->canBeViewedBy(auth()->user()))
        {{ $person->name }}
        @if($person->last_name)
            {{ $person->last_name }} (z d. {{ $person->family_name }})
        @else
            {{ $person->family_name }}
        @endif
    @else
        [{{ __('misc.hidden') }}]
    @endif

@if($person->dead)
    </i>
@endif

<small class="text-lg">
    [№{{ $person->id }}]
</small>
