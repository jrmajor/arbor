@php
    if (! isset($active)) {
        $active = [
            'type' => null,
            'letter' => null,
        ];
    }
@endphp

<div>
    <h2>{{ __('people.index.by_family_name') }}:</h2>

    <ul class="col-count-3 xs:col-count-4 sm:col-count-5 md:col-count-6 lg:col-count-8">
        @foreach(App\Models\Person::letters('family') as $letter)
            <li>
                @if($active['letter'] == $letter->letter && $active['type'] == 'f')
                    <strong>
                @endif
                <a href="{{ route('people.letter', ['type' => 'f', 'letter' => urlencode($letter->letter)]) }}" class="a">
                    {{ $letter->letter }} <small>[{{ $letter->total }}]</small>
                </a>
                @if($active['letter'] == $letter->letter && $active['type'] == 'f')
                    </strong>
                @endif
            </li>
        @endforeach
    </ul>
</div>

<div class="mt-4">
    <h2>{{ __('people.index.by_last_name') }}:</h2>

    <ul class="col-count-3 xs:col-count-4 sm:col-count-5 md:col-count-6 lg:col-count-8">
        @foreach(App\Models\Person::letters('last') as $letter)
            <li>
                @if($active['letter'] == $letter->letter && $active['type'] == 'l')
                    <strong>
                @endif
                <a href="{{ route('people.letter', ['type' => 'l', 'letter' => urlencode($letter->letter)]) }}" class="a">
                    {{ $letter->letter }} <small>[{{ $letter->total }}]</small>
                </a>
                @if($active['letter'] == $letter->letter && $active['type'] == 'l')
                    </strong>
                @endif
            </li>
        @endforeach
    </ul>
</div>
