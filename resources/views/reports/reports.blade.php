@extends('layouts.app')

@section('title-bar', 'Reports')

@section('content')
    <strong>should be dead:</strong>
    <ul>
        @forelse ($shouldBeDead as $person)
            <li><x-name :person="$person"/></li>
        @empty
            <li>all ok</li>
        @endforelse
    </ul>

    <strong>visible alive:</strong>
    <ul>
        @forelse ($visibleAlive as $person)
            <li><x-name :person="$person"/></li>
        @empty
            <li>all ok</li>
        @endforelse
    </ul>

    <strong>invisible dead:</strong>
    <ul>
        @forelse ($invisibleDead as $person)
            <li><x-name :person="$person"/></li>
        @empty
            <li>all ok</li>
        @endforelse
    </ul>
@endsection
