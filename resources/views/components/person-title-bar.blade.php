<div class="flex items-center">
    <div class="mr-1">
        @if($person->sex == 'xx')
            <svg class="stroke-current stroke-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 32">
                <circle cx="9" cy="9.5" r="7.5" fill="none"/>
                <path fill="none" d="M9 17V30M2 24H16"/>
            </svg>
        @elseif($person->sex == 'xy')
            <svg class="stroke-current stroke-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 32">
                <circle cx="9" cy="22.5" r="7.5" fill="none"/>
                <path fill="none" d="M9 15V2M9 2m-8 8L9 2l8 8"/>
            </svg>
        @endif
    </div>

    <div>
        @if($person->dead)
            <i>
        @endif

            @if($person->canBeViewedBy(auth()->user()))
                <span class="font-normal">{{ $person->name }}</span>
                @if($person->last_name)
                    {{ $person->last_name }} ({{ $person->family_name }})
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
    </div>
</div>
