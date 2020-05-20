@php
    if (! isset($active))
        $active = [
            'type' => null,
            'letter' => null,
        ];
@endphp

@extends('layouts.app')

@section('content')

    <x-letters :active="$active"/>

    @if(isset($list))
        <ul>
            @foreach($list as $person)
                <li>
                    <x-name :person="$person" :bold="$active['type']"/>
                </li>
            @endforeach
        </ul>
    @endif

@endsection
