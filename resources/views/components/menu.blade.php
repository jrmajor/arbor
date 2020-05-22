<nav
    class="mb-1 p-4 bg-white shadow-md"
    x-data="{ open: false }">
    <div class="container mx-auto">
        <div class="px-3 flex items-center justify-between flex-wrap">

            <div>
                <a href="{{ route('people.index') }}"
                    class="p-2 text-gray-800 rounded hover:text-black hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out">
                    <img class="inline-block h-4" src="{{ asset('img/genealogy.png') }}" height="15px">&nbsp;{{ __('misc.menu.tree') }}
                </a>
            </div>

            <button
                @click="open = ! open"
                type="button"
                class="block md:hidden p-2 -my-2 text-gray-800 hover:text-black focus:outline-none">
                <svg class="h-6 w-6 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path x-show="open" fill-rule="evenodd" clip-rule="evenodd" d="M18.278 16.864a1 1 0 0 1-1.414 1.414l-4.829-4.828-4.828 4.828a1 1 0 0 1-1.414-1.414l4.828-4.829-4.828-4.828a1 1 0 0 1 1.414-1.414l4.829 4.828 4.828-4.828a1 1 0 1 1 1.414 1.414l-4.828 4.829 4.828 4.828z"/>
                    <path x-show="! open" fill-rule="evenodd" d="M4 5h16a1 1 0 0 1 0 2H4a1 1 0 1 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2z"/>
                  </svg>
            </button>

            <div
                class="flex-col w-full mt-4 md:flex md:flex-row md:w-auto md:mt-0"
                :class="{ 'flex': open, 'hidden': ! open }"
                @click.away="open = false">

                @if($user->canWrite())
                    <a
                        href="{{ route('people.create') }}"
                        class="px-2 py-1 text-gray-800 rounded hover:text-black hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out">
                        {{ __('misc.menu.add_person') }}
                    </a>
                @endif

                @if($user->isSuperAdmin())
                    <a
                        href="{{ route('activities.logins') }}"
                        class="px-2 py-1 text-gray-800 rounded hover:text-black hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out">
                        {{ __('misc.menu.logins') }}
                    </a>
                    <a
                        href="{{ route('activities.models') }}"
                        class="px-2 py-1 text-gray-800 rounded hover:text-black hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out">
                        {{ __('misc.menu.activities') }}
                    </a>
                    <a
                        href="{{ route('reports') }}"
                        class="px-2 py-1 text-gray-800 rounded hover:text-black hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out">
                        {{ __('misc.menu.reports') }}
                    </a>
                @endif

                <form
                    action="{{ route('locale.set') }}" method="POST"
                    class="px-2 py-1 text-gray-800 text-sm flex items-center">
                    @csrf
                    {{ __('misc.language') }}:&nbsp;
                    @unless(app()->isLocale('en'))
                        <button name="language" value="en" class="btn leading-none text-xs px-2 py-1 normal-case font-normal tracking-normal">
                            EN
                        </button>
                    @endunless
                    @unless(app()->isLocale('pl'))
                        <button name="language" value="pl" class="btn leading-none text-xs px-2 py-1 normal-case font-normal tracking-normal">
                            PL
                        </button>
                    @endunless
                </form>

                @guest
                    <a href="{{ route('login') }}"
                        class="px-2 py-1 text-gray-800 rounded hover:text-black hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out">
                        <img class="inline-block h-4" src="{{ asset('img/import.png') }}" height="15px">&nbsp;{{ __('misc.menu.login') }}
                    </a>
                @else
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                        class="px-2 py-1 text-gray-800 rounded hover:text-black hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out">
                        <img class="inline-block h-4" src="{{ asset('img/export.png') }}" height="15px">&nbsp;{{ __('misc.menu.logout') }}
                        <small>[{{ $user->username }}]</small>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none">
                        @csrf
                    </form>
                @endguest
            </div>

        </div>
    </div>
</nav>
