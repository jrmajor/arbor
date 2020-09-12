@extends('layouts.app')

@section('title', 'Reports')

@section('content')

    <div class="flex flex-col md:flex-row space-x-2 space-y-2">

        <main class="flex-grow md:w-1/2 space-y-5">

            <div>
                <h2 class="mb-2 leading-none text-xl font-medium">Should be dead</h2>
                <div class="w-full p-6 bg-white rounded-lg shadow">
                    <ul>
                        @forelse ($shouldBeDead as $person)
                            <li><x-name :person="$person"/></li>
                        @empty
                            <li>all ok</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div>
                <h2 class="mb-2 leading-none text-xl font-medium">Visible alive</h2>
                <div class="w-full p-6 bg-white rounded-lg shadow">
                    <ul>
                        @forelse ($visibleAlive as $person)
                            <li><x-name :person="$person"/></li>
                        @empty
                            <li>all ok</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div>
                <h2 class="mb-2 leading-none text-xl font-medium">Invisible dead</h2>
                <div class="w-full p-6 bg-white rounded-lg shadow">
                    <ul>
                        @forelse ($invisibleDead as $person)
                            <li><x-name :person="$person"/></li>
                        @empty
                            <li>all ok</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </main>

        <div class="flex-shrink-0 p-1">
            <x-dashboard-menu active="reports"/>
        </div>

    </div>

@endsection
