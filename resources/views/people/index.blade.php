@extends('layouts.app')

@section('content')

    <main class="p-6 bg-white rounded-lg shadow">

        <x-letters :active="$active ?? null"/>

        @if(isset($list))
            <hr class="-mx-6 my-5 border-t-2 border-dashed">

            <ul>
                @foreach($list as $person)
                    <li>
                        <x-name :person="$person" :bold="isset($active) ? $active['type'] : null"/>
                    </li>
                @endforeach
            </ul>
        @endif

    </main>

@endsection
