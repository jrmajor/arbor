@extends('layouts.app')

@section('content')

    <div class="p-4 bg-white rounded-lg shadow-lg">

        <x-letters :active="$active ?? null"/>

        @if(isset($list))
            <hr class="-mx-4 my-3 border-t-2 border-dashed">

            <ul>
                @foreach($list as $person)
                    <li>
                        <x-name :person="$person" :bold="isset($active) ? $active['type'] : null"/>
                    </li>
                @endforeach
            </ul>
        @endif

    </div>

@endsection
