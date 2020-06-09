@extends('layouts.app')

@section('title-bar', 'Reports')

@section('sidebar-menu')
    <x-dashboard-menu active="reports"/>
@endsection

@section('content')
    <strong>Should be dead</strong>
    <ul>
        @forelse ($shouldBeDead as $person)
            <li><x-name :person="$person"/></li>
        @empty
            <li>all ok</li>
        @endforelse
    </ul>

    <strong>Visible alive</strong>
    <ul>
        @forelse ($visibleAlive as $person)
            <li><x-name :person="$person"/></li>
        @empty
            <li>all ok</li>
        @endforelse
    </ul>

    <strong>Invisible dead</strong>
    <ul>
        @forelse ($invisibleDead as $person)
            <li><x-name :person="$person"/></li>
        @empty
            <li>all ok</li>
        @endforelse
    </ul>
@endsection
