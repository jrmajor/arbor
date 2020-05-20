@extends('layouts.app')

@section('content')
<strong>should be dead:</strong>
<ul>
@forelse ($shouldBeDead as $person)
    <li>@component('components.name', ['person' => $person])@endcomponent</li>
@empty
    <li>all ok</li>
@endforelse
</ul>

<strong>visible alive:</strong>
<ul>
@forelse ($visibleAlive as $person)
    <li>@component('components.name', ['person' => $person])@endcomponent</li>
@empty
    <li>all ok</li>
@endforelse
</ul>

<strong>invisible dead:</strong>
<ul>
@forelse ($invisibleDead as $person)
    <li>@component('components.name', ['person' => $person])@endcomponent</li>
@empty
    <li>all ok</li>
@endforelse
</ul>
@endsection
