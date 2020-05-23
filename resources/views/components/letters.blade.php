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
    <small>
        [{{ __('people.index.total') }}: <strong>{{ App\Person::count() }}</strong>]
    </small>
    <br>&nbsp;&nbsp;

    @foreach(App\Person::letters('family') as $letter)
        @if (! $loop->first)
            &middot;
        @endif

        @if($active['letter'] == $letter->letter && $active['type'] == 'f')
            <strong>
        @endif
        <a href="{{ route('people.letter', ['type' => 'f', 'letter' => urlencode($letter->letter)]) }}" class="a">
            {{ $letter->letter }} <small>[{{ $letter->total }}]</small>
        </a>
        @if($active['letter'] == $letter->letter && $active['type'] == 'f')
            </strong>
        @endif
    @endforeach
</div>

<div>
    {{ __('people.index.by_last_name') }}:
    <small>
        [{{ __('people.index.total') }}: <strong>{{ App\Person::count() }}</strong>]
    </small>
    <br>&nbsp;&nbsp;

    @foreach(App\Person::letters('last') as $letter)
        @if (! $loop->first)
            &middot;
        @endif

        @if($active['letter'] == $letter->letter && $active['type'] == 'l')
            <strong>
        @endif
        <a href="{{ route('people.letter', ['type' => 'l', 'letter' => urlencode($letter->letter)]) }}" class="a">
            {{ $letter->letter }} <small>[{{ $letter->total }}]</small>
        </a>
        @if($active['letter'] == $letter->letter && $active['type'] == 'l')
            </strong>
        @endif
    @endforeach
</div>
