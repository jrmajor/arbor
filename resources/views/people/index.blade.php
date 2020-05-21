@php
    if (! isset($active))
        $active = [
            'type' => null,
            'letter' => null,
        ];
@endphp

@extends('layouts.app')

@section('raw')

    <div class="my-2 mx-1 p-4 bg-white rounded-lg shadow-lg">
        <x-letters :active="$active"/>

    @if(isset($list))
            <hr class="-mx-4 my-3 border-t-2 border-dashed">

            <ul>
                @foreach($list as $person)
                    <li>
                        <x-name :person="$person" :bold="$active['type']"/>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

@endsection
