@php
    if (! isset($active)) {
        $active = [
            'type' => null,
            'letter' => null,
        ];
    }
@endphp

<div>
    {{ __('people.index.by_family_name') }}:
    <small class="text-muted">
        [{{ __('people.index.total') }}: <strong>{{ App\Person::count() }}</strong>]
    </small>
    @if(optional(auth()->user())->canWrite())
        <a href="{{ route('people.create') }}" class="a"
            data-tippy-content="{{ __('people.add_a_new_person') }}">
            <small>[+]</small>
        </a>
    @endif
    <br>&nbsp;&nbsp;

    @foreach(App\Person::letters('family') as $letter)
        @if (! $loop->first)
            &middot;
        @endif

        @if($active['letter'] == $letter->letter && $active['type'] == 'f')
            <strong>
        @endif
        <a href="{{ route('people.letter', ['type' => 'f', 'letter' => urlencode($letter->letter)]) }}" class="a">
            {{ $letter->letter }} <small class="text-muted">[{{ $letter->total }}]</small>
        </a>
        @if($active['letter'] == $letter->letter && $active['type'] == 'f')
            </strong>
        @endif
    @endforeach
</div>

<div>
    {{ __('people.index.by_last_name') }}:
    <small class="text-muted">
        [{{ __('people.index.total') }}: <strong>{{ App\Person::count() }}</strong>]
    </small>
    @if(optional(auth()->user())->canWrite())
        <a href="{{ route('people.create') }}" class="a"
            data-tippy-content="{{ __('people.add_a_new_person') }}">
            <small>[+]</small>
        </a>
    @endif
    <br>&nbsp;&nbsp;

    @foreach(App\Person::letters('last') as $letter)
        @if (! $loop->first)
            &middot;
        @endif

        @if($active['letter'] == $letter->letter && $active['type'] == 'l')
            <strong>
        @endif
        <a href="{{ route('people.letter', ['type' => 'l', 'letter' => urlencode($letter->letter)]) }}" class="a">
            {{ $letter->letter }} <small class="text-muted">[{{ $letter->total }}]</small>
        </a>
        @if($active['letter'] == $letter->letter && $active['type'] == 'l')
            </strong>
        @endif
    @endforeach
</div>
