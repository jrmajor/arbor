@php
    if (! isset($active))
        $active = [
            'type' => null,
            'letter' => null,
        ];
@endphp

@extends('layouts.app')

@section('content')

    @component('components.letters', ['active' => $active])
    @endcomponent

    @if(isset($list))
        <ul>
            @foreach($list as $person)
                <li>
                    @component('components.name', ['person' => $person, 'bold' => $active['type']])
                    @endcomponent
                </li>
            @endforeach
        </ul>
    @endif

@endsection
