<nav class="m-1 p-4 bg-white rounded-lg shadow flex flex-row items-center justify-between">
    <div>
        <a href="{{ route('people.index') }}"
            class="p-2 text-gray-800 rounded hover:text-black hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out" >arbor</a>
        <a href="{{ route('people.index') }}"
            class="p-2 text-gray-800 rounded hover:text-black hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out">
            <img class="inline-block h-4" src="{{ asset('img/genealogy.png') }}" height="15px">&nbsp;{{ __('misc.menu.tree') }}
        </a>
    </div>

    @if($user->canWrite())
        <div class="hidden md:block">
            <a href="{{ route('people.create') }}" class="a">
                <small>{{ __('misc.menu.add_person') }}</small>
            </a>
            | <a href="{{ route('marriages.create') }}" class="a">
                <small>{{ __('misc.menu.add_marriage') }}</small>
            </a>
            @if($user->isSuperAdmin())
                {{--| <a href="{{ route('users.create') }}" class="a">
                <small>{{ __('misc.menu.add_user') }}</small>
                </a>
                | <a href="{{ route('users.index') }}" class="a">
                    <small>{{ __('misc.menu.manage_users') }}</small>
                </a>--}}
                | <a href="{{ route('activities.logins') }}" class="a">
                    <small>{{ __('misc.menu.logins') }}</small>
                </a>
                | <a href="{{ route('activities.models') }}" class="a">
                    <small>{{ __('misc.menu.activities') }}</small>
                </a>
                | <a href="{{ route('reports') }}" class="a">
                    <small>{{ __('misc.menu.reports') }}</small>
                </a>
            @endif
        </div>
    @endif

    <div class="flex">
        <div class="px-2">
            <form action="{{ route('locale.set') }}" method="POST">
                @csrf
                <small class="hidden md:inline">{{ __('misc.language') }}:</small>
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
                <a href="{{ route('login') }}"
                    class="p-2 text-gray-800 rounded hover:text-black hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out">
                    <img class="inline-block h-4" src="{{ asset('img/import.png') }}" height="15px">&nbsp;{{ __('misc.menu.login') }}
                </a>
            @else
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                    class="p-2 text-gray-800 rounded hover:text-black hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out">
                    <img class="inline-block h-4" src="{{ asset('img/export.png') }}" height="15px">&nbsp;{{ __('misc.menu.logout') }}
                    <small class="hidden md:inline">[{{ $user->username }}]</small>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none">
                    @csrf
                </form>
            @endguest
        </div>
    </div>
</nav>
