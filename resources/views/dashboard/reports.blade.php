@extends('layouts.app')

@section('content')

    <div class="flex flex-col md:flex-row space-x-2 space-y-2">

        <div class="flex-grow space-y-2">

            <div>
                <h2 class="mx-2 mb-1 mt-5 leading-none text-xl font-medium">Should be dead</h2>
                <div class="w-full p-4 bg-white rounded-lg shadow-lg">
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
                <h2 class="mx-2 mb-1 mt-5 leading-none text-xl font-medium">Visible alive</h2>
                <div class="w-full p-4 bg-white rounded-lg shadow-lg">
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
                <h2 class="mx-2 mb-1 mt-5 leading-none text-xl font-medium">Invisible dead</h2>
                <div class="w-full p-4 bg-white rounded-lg shadow-lg">
                    <ul>
                        @forelse ($invisibleDead as $person)
                            <li><x-name :person="$person"/></li>
                        @empty
                            <li>all ok</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>

        <div class="flex-shrink-0 p-1">
            <x-dashboard-menu active="reports"/>
        </div>

    </div>

@endsection
