<nav class="mb-2 p-4 bg-gray-200 rounded flex flex-row items-center justify-between">
    <div>
        <a class="p-2 text-gray-800 rounded hover:text-black hover:no-underline hover:bg-gray-300" href="{{ route('people.index') }}">arbor</a>
        <a class="p-2 text-gray-800 rounded hover:text-black hover:no-underline hover:bg-gray-300" href="{{ route('people.index') }}">
            <img class="inline-block h-4" src="{{ asset('img/genealogy.png') }}" height="15px">&nbsp;{{ __('misc.menu.tree') }}
        </a>
    </div>

    @if(optional(auth()->user())->canWrite())
        <div>
            <a href="{{ route('people.create') }}"><small>{{ __('misc.menu.add_person') }}</small></a>
            | <a href="{{ route('marriages.create') }}"><small>{{ __('misc.menu.add_marriage') }}</small></a>
            {{--@if(optional(auth()->user())->isSuperAdmin())
                | <a href="{{ route('users.create') }}"><small>{{ __('misc.menu.add_user') }}</small></a>
                | <a href="{{ route('users.index') }}"><small>{{ __('misc.menu.manage_users') }}</small></a>
            @endif--}}
        </div>
    @endif

    <div class="flex">
        <div class="px-2">
            <form action="{{ route('locale.set') }}" method="POST">
                @csrf
                <small>{{ __('misc.language') }}:</small>
                @unless(App::isLocale('en'))
                    <button name="language" value="en" class="leading-none text-xs px-2 py-1 normal-case font-normal tracking-normal">
                        EN
                    </button>
                @endunless
                @unless(App::isLocale('pl'))
                    <button name="language" value="pl" class="leading-none text-xs px-2 py-1 normal-case font-normal tracking-normal">
                        PL
                    </button>
                @endunless
            </form>
        </div>
        <div>
            @guest
                <a class="p-2 text-gray-800 rounded hover:text-black hover:no-underline hover:bg-gray-300" href="{{ route('login') }}">
                    <img class="inline-block h-4" src="{{ asset('img/import.png') }}" height="15px">&nbsp;{{ __('misc.menu.login') }}
                </a>
            @else
                <a class="p-2 text-gray-800 rounded hover:text-black hover:no-underline hover:bg-gray-300" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    <img class="inline-block h-4" src="{{ asset('img/export.png') }}" height="15px">&nbsp;{{ __('misc.menu.logout') }} <small>[{{ Auth::user()->username }}]</small>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none">
                    @csrf
                </form>
            @endguest
        </div>
    </div>
</nav>
