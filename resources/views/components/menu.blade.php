<nav
    class="mb-1 p-4 bg-white shadow-md"
    x-data="{ open: false }">
    <div class="container mx-auto">
        <div class="px-3 flex items-center justify-between flex-wrap">

            <div class="flex items-center">
                <a href="{{ route('people.index') }}"
                    class="px-2 py-1 text-gray-700 rounded hover:text-gray-900 hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out flex items-center">
                    <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path d="M460.8 64V38.4H345.6v64H140.8v140.8H51.2v25.6h89.6v140.8h204.8v64h115.2V448h-89.6V345.6h89.6V320H345.6v64H166.4V128h179.2v64h115.2v-25.6h-89.6V64z"/>
                        <path d="M0 179.8h102.4v152.4H0zM409.6 128H512v102.4H409.6zM409.6 0H512v102.4H409.6zM204.8 64h102.4v102.4H204.8zM204.8 345.6h102.4V448H204.8zM409.6 409.6H512V512H409.6zM409.6 281.6H512V384H409.6z"/>
                    </svg>
                    {{ __('misc.menu.tree') }}
                </a>
            </div>

            <button
                @click="open = ! open"
                type="button"
                class="block lg:hidden p-2 -my-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                <svg class="fill-current h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path x-show="open" d="M10 8.586L2.929 1.515 1.515 2.929 8.586 10l-7.071 7.071 1.414 1.414L10 11.414l7.071 7.071 1.414-1.414L11.414 10l7.071-7.071-1.414-1.414L10 8.586z"/>
                    <path x-show="! open" d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/>
                  </svg>
            </button>

            <div
                class="flex-col w-full mt-4 lg:flex lg:flex-row lg:w-auto lg:mt-0 lg:items-center"
                :class="{ 'flex': open, 'hidden': ! open }"
                @click.away="open = false">

                @if(request()->route()->getName() != 'search')
                    <form action="{{ route('search') }}"
                        class="relative mx-2 mb-2 lg:mb-0">
                        <input type="search" name="s" required></input>
                        <button class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 hover:text-gray-900 transition-colors duration-200 ease-out">
                            <svg class="fill-current h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/>
                            </svg>
                        </button>
                    </form>
                @endif

                @if($user->canWrite())
                    <a
                        href="{{ route('people.create') }}"
                        class="px-2 py-1 text-gray-700 rounded hover:text-gray-900 hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out flex items-center">
                        <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M2 6H0v2h2v2h2V8h2V6H4V4H2v2zm7 0a3 3 0 0 1 6 0v2a3 3 0 0 1-6 0V6zm11 9.14A15.93 15.93 0 0 0 12 13c-2.91 0-5.65.78-8 2.14V18h16v-2.86z"/>
                        </svg>
                        {{ __('misc.menu.add_person') }}
                    </a>
                @endif

                @if($user->isSuperAdmin())
                    <a
                        href="{{ route('activities.logins') }}"
                        class="px-2 py-1 text-gray-700 rounded hover:text-gray-900 hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out flex items-center">
                        <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M0 3h20v2H0V3zm0 4h20v2H0V7zm0 4h20v2H0v-2zm0 4h20v2H0v-2z"/>
                        </svg>
                        {{ __('misc.menu.logins') }}
                    </a>

                    <a
                        href="{{ route('activities.models') }}"
                        class="px-2 py-1 text-gray-700 rounded hover:text-gray-900 hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out flex items-center">
                        <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M0 3h20v2H0V3zm0 4h20v2H0V7zm0 4h20v2H0v-2zm0 4h20v2H0v-2z"/>
                        </svg>
                        {{ __('misc.menu.activities') }}
                    </a>

                    <a
                        href="{{ route('reports') }}"
                        class="px-2 py-1 text-gray-700 rounded hover:text-gray-900 hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out flex items-center">
                        <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M4.13 12H4a2 2 0 1 0 1.8 1.11L7.86 10a2.03 2.03 0 0 0 .65-.07l1.55 1.55a2 2 0 1 0 3.72-.37L15.87 8H16a2 2 0 1 0-1.8-1.11L12.14 10a2.03 2.03 0 0 0-.65.07L9.93 8.52a2 2 0 1 0-3.72.37L4.13 12zM0 4c0-1.1.9-2 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4z"/>
                        </svg>
                        {{ __('misc.menu.reports') }}
                    </a>
                @endif

                <form
                    action="{{ route('locale.set') }}" method="POST"
                    class="px-2 py-1 text-gray-700 text-sm flex items-center">
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
                    <a
                        href="mailto:jeremiah.major@npng.pl"
                        class="px-2 py-1 text-gray-700 rounded hover:text-gray-900 hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out flex items-center">
                        <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M13.6 13.47A4.99 4.99 0 0 1 5 10a5 5 0 0 1 8-4V5h2v6.5a1.5 1.5 0 0 0 3 0V10a8 8 0 1 0-4.42 7.16l.9 1.79A10 10 0 1 1 20 10h-.18.17v1.5a3.5 3.5 0 0 1-6.4 1.97zM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        </svg>
                        {{ __('misc.menu.contact') }}
                    </a>

                    <a href="{{ route('login') }}"
                        class="px-2 py-1 text-gray-700 rounded hover:text-gray-900 hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out flex items-center">
                        <svg class="stroke-current h-5 w-5 -ml-1 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M192 176v-40a40 40 0 0140-40h160a40 40 0 0140 40v240a40 40 0 01-40 40H240c-22.09 0-48-17.91-48-40v-40" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/>
                            <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="38" d="M288 336l80-80-80-80M80 256h272"/>
                        </svg>
                        {{ __('misc.menu.login') }}
                    </a>
                @else
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                        class="px-2 py-1 text-gray-700 rounded hover:text-gray-900 hover:no-underline hover:bg-gray-100 transition-colors duration-200 ease-out flex items-center">
                        <svg class="stroke-current h-5 w-5 -ml-1 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M304 336v40a40 40 0 01-40 40H104a40 40 0 01-40-40V136a40 40 0 0140-40h152c22.09 0 48 17.91 48 40v40M368 336l80-80-80-80M176 256h256" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="38"/></svg>
                        </svg>
                        {{ __('misc.menu.logout') }}
                        <small class="ml-1">[{{ $user->username }}]</small>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none">
                        @csrf
                    </form>
                @endguest

            </div>

        </div>
    </div>
</nav>
