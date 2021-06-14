<nav
  class="mb-1 bg-white shadow-md"
  x-data="{
    open: false,
    dropdown: false
  }"
>
  <div class="container mx-auto">
    <div class="px-3 flex items-center justify-between flex-wrap">

      <div class="flex items-center">
        <a href="{{ route('people.index') }}"
          class="px-4 pt-4 pb-3 md:pt-5 md:pb-4 lg:pt-6 lg:pb-4 text-gray-800
            hover:text-gray-900 hover:bg-gray-100 focus:bg-cool-gray-100
            border-b-2 border-solid border-transparent
            {{ $active == 'people.index' ? 'border-blue-500' : 'hover:border-gray-400 focus:border-gray-400 active:border-blue-500' }}
            focus:outline-none hover:no-underline
            transition-colors duration-200
            flex items-center"
        >
          <svg class="fill-current h-4 w-4 mr-2" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
            <path d="M460.8 64V38.4H345.6v64H140.8v140.8H51.2v25.6h89.6v140.8h204.8v64h115.2V448h-89.6V345.6h89.6V320H345.6v64H166.4V128h179.2v64h115.2v-25.6h-89.6V64z"/>
            <path d="M0 179.8h102.4v152.4H0zM409.6 128H512v102.4H409.6zM409.6 0H512v102.4H409.6zM204.8 64h102.4v102.4H204.8zM204.8 345.6h102.4V448H204.8zM409.6 409.6H512V512H409.6zM409.6 281.6H512V384H409.6z"/>
          </svg>
          {{ __('misc.menu.tree') }}
        </a>
      </div>

      <button
        x-on:click="open = ! open"
        type="button"
        class="block lg:hidden px-4 pt-4 pb-3 md:pt-5 md:pb-4 -my-2 text-gray-800 hover:text-gray-900 focus:outline-none"
      >
        <svg class="fill-current h-5 w-5" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
          <path x-show="! open" d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/>
          <path x-show="open" d="M10 8.586L2.929 1.515 1.515 2.929 8.586 10l-7.071 7.071 1.414 1.414L10 11.414l7.071 7.071 1.414-1.414L11.414 10l7.071-7.071-1.414-1.414L10 8.586z"/>
          </svg>
      </button>

      <div
        class="flex-col w-full mt-2 pb-2 lg:flex lg:flex-row lg:w-auto lg:mt-0 lg:pb-0 lg:items-center"
        :class="{ 'flex': open, 'hidden': ! open }"
        x-on:click.away="open = false"
      >

        @if ($active != 'search')
          <form
            role="search"
            class="relative mb-2 lg:mb-0 lg:mt-1 lg:mr-3 lg:w-96"
            x-data="menuSearchData()"
            x-on:submit="$event.preventDefault()"
          >
            <input
              type="search" class="form-input w-full h-9"
              x-model="search" name="s" autocomplete="off"
              x-on:input="findPeople" x-on:keydown.enter="enter" x-on:keydown.escape="$event.target.blur()"
              x-on:keydown.arrow-up="arrow('up')" x-on:keydown.arrow-down="arrow('down')"
              x-on:focus="open = shouldCloseOnBlur = true" x-on:blur="closeDropdown"
            >
            <button class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 active:text-gray-900 transition-colors duration-200">
              <svg class="fill-current h-5 w-5" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/>
              </svg>
            </button>
            <template x-if="open && ! (search === '' && people.length === 0)">
              <ul class="absolute mt-2 z-50 py-1 w-full text-gray-800 bg-white rounded-md shadow-md border border-gray-300"
                x-on:mousedown="shouldCloseOnBlur = false"
              >
                <template x-if="people.length === 0">
                  <li class="w-full px-3 py-1 text-gray-600">
                    {{ __('misc.no_results') }}
                  </li>
                </template>
                <template x-for="(person, index) in people" x-key="person.id">
                  <a :href="person.url" x-on:click="open = false">
                    <li
                      x-on:mouseover="hovered = index"
                      class="select-none w-full px-3 py-1 text-gray-800 flex justify-between items-center"
                      :class="{ 'bg-cool-gray-100': hovered === index }"
                    >
                      <span>
                        <span x-text="person.name"></span>
                        <small x-show="person.dates" x-text="'(' + person.dates + ')'"></small>
                      </span>
                      @if ($user?->isSuperAdmin())
                        <small x-text="'№' + person.id" class="tabular-nums"></small>
                      @endif
                    </li>
                  </a>
                </template>
              </ul>
            </template>
          </form>
        @endif

        @if ($user?->canWrite())
          <a
            href="{{ route('people.create') }}"
            class="px-3 py-1 lg:pt-6 lg:pb-4 text-gray-800
              hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 lg:hover:bg-gray-100 lg:focus:bg-cool-gray-100
              rounded lg:rounded-none uppercase lg:normal-case
              border-b-2 border-solid border-transparent
              {{ $active === 'people.create' ? 'lg:border-blue-500' : 'lg:hover:border-gray-400 lg:focus:border-gray-400 lg:active:border-blue-500' }}
              focus:outline-none hover:no-underline
              transition-colors duration-200"
          >
            <div class="w-full {{ $active == 'people.create' ? 'border-b-2 border-dotted border-blue-500 lg:border-none' : '' }} flex items-center">
              <svg class="fill-current h-4 w-4 mr-2 lg:hidden" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 6H0v2h2v2h2V8h2V6H4V4H2v2zm7 0a3 3 0 0 1 6 0v2a3 3 0 0 1-6 0V6zm11 9.14A15.93 15.93 0 0 0 12 13c-2.91 0-5.65.78-8 2.14V18h16v-2.86z"/>
              </svg>
              {{ __('misc.menu.add_person') }}
            </div>
          </a>
        @endif

        @if ($user?->isSuperAdmin())
          <a
            href="{{ route('dashboard.users') }}"
            class="px-3 py-1 lg:pt-6 lg:pb-4 text-gray-800
              hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 lg:hover:bg-gray-100 lg:focus:bg-cool-gray-100
              rounded lg:rounded-none uppercase lg:normal-case
              border-b-2 border-solid border-transparent
              {{ $active == 'dashboard' ? 'lg:border-blue-500' : 'lg:hover:border-gray-400 lg:focus:border-gray-400 lg:active:border-blue-500' }}
              focus:outline-none hover:no-underline
              transition-colors duration-200"
          >
            <div class="w-full {{ $active == 'dashboard' ? 'border-b-2 border-dotted border-blue-500 lg:border-none' : '' }} flex items-center">
              <svg class="fill-current h-4 w-4 mr-2 lg:hidden" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm-5.6-4.29a9.95 9.95 0 0 1 11.2 0 8 8 0 1 0-11.2 0zm6.12-7.64l3.02-3.02 1.41 1.41-3.02 3.02a2 2 0 1 1-1.41-1.41z"/>
              </svg>
              {{ __('misc.menu.dashboard') }}
            </div>
          </a>
        @endif

        @guest
          <a
            href="mailto:jeremiah.major@npng.pl"
            class="px-3 py-1 lg:pt-6 lg:pb-4 text-gray-800
              hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 lg:hover:bg-gray-100 lg:focus:bg-cool-gray-100
              rounded lg:rounded-none uppercase lg:normal-case
              border-b-2 border-solid border-transparent
              lg:hover:border-gray-400 lg:focus:border-gray-400 lg:active:border-blue-500
              focus:outline-none hover:no-underline
              transition-colors duration-200
              flex items-center"
          >
            <svg class="fill-current h-4 w-4 mr-2 lg:hidden" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path d="M13.6 13.47A4.99 4.99 0 0 1 5 10a5 5 0 0 1 8-4V5h2v6.5a1.5 1.5 0 0 0 3 0V10a8 8 0 1 0-4.42 7.16l.9 1.79A10 10 0 1 1 20 10h-.18.17v1.5a3.5 3.5 0 0 1-6.4 1.97zM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
            </svg>
            {{ __('misc.menu.contact') }}
          </a>

          <a
            href="{{ route('login') }}"
            class="px-3 py-1 lg:pt-6 lg:pb-4 text-gray-800
              hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 lg:hover:bg-gray-100 lg:focus:bg-cool-gray-100
              rounded lg:rounded-none uppercase lg:normal-case
              border-b-2 border-solid border-transparent
              lg:hover:border-gray-400 lg:focus:border-gray-400 lg:active:border-blue-500
              focus:outline-none hover:no-underline
              transition-colors duration-200
              flex items-center"
          >
            <svg class="stroke-current h-5 w-5 -ml-1 mr-1 lg:hidden" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
              <path d="M192 176v-40a40 40 0 0140-40h160a40 40 0 0140 40v240a40 40 0 01-40 40H240c-22.09 0-48-17.91-48-40v-40" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="38"/>
              <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="38" d="M288 336l80-80-80-80M80 256h272"/>
            </svg>
            {{ __('misc.menu.login') }}
          </a>
        @else
          <a
            href="{{ route('settings') }}"
            class="lg:hidden px-3 py-1 text-gray-800
              hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-gray-100
              rounded uppercase
              border-b-2 border-solid border-transparent
              focus:outline-none hover:no-underline
              transition-colors duration-200"
          >
            <div class="w-full {{ $active == 'settings' ? 'border-b-2 border-dotted border-blue-500' : '' }} flex items-center">
              <svg class="fill-current h-4 w-4 mr-2" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M3.94 6.5L2.22 3.64l1.42-1.42L6.5 3.94c.52-.3 1.1-.54 1.7-.7L9 0h2l.8 3.24c.6.16 1.18.4 1.7.7l2.86-1.72 1.42 1.42-1.72 2.86c.3.52.54 1.1.7 1.7L20 9v2l-3.24.8c-.16.6-.4 1.18-.7 1.7l1.72 2.86-1.42 1.42-2.86-1.72c-.52.3-1.1.54-1.7.7L11 20H9l-.8-3.24c-.6-.16-1.18-.4-1.7-.7l-2.86 1.72-1.42-1.42 1.72-2.86c-.3-.52-.54-1.1-.7-1.7L0 11V9l3.24-.8c.16-.6.4-1.18.7-1.7zM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
              </svg>
              {{ __('misc.menu.settings') }}
            </div>
          </a>

          <a
            href="{{ route('logout') }}"
            onclick="event.preventDefault();document.getElementById('logout-form').submit();"
            class="lg:hidden px-3 py-1 text-gray-800
              hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-gray-100
              rounded uppercase
              border-b-2 border-solid border-transparent
              focus:outline-none hover:no-underline
              transition-colors duration-200
              flex items-center"
          >
            <svg class="stroke-current h-5 w-5 mr-1 lg:hidden" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
              <path d="M304 336v40a40 40 0 01-40 40H104a40 40 0 01-40-40V136a40 40 0 0140-40h152c22.09 0 48 17.91 48 40v40M368 336l80-80-80-80M176 256h256" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="38"/>
            </svg>
            <span>
              {{ __('misc.menu.logout') }}<small class="ml-1 normal-case">({{ $user->username }})</small>
            </span>
          </a>

          <div class="hidden lg:block relative">
            <button
              x-on:click="dropdown = ! dropdown"
              class="px-3 pt-6 pb-4 text-gray-800
                hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-cool-gray-100
                border-b-2 border-solid border-transparent
                hover:border-gray-400 focus:border-gray-400 active:border-blue-500
                focus:outline-none hover:no-underline
                transition-color duration-200
                flex items-center"
              :class="{ 'border-blue-500': dropdown, 'hover:border-blue-500': dropdown, 'focus:border-blue-500': dropdown }"
            >
              {{ $user->username }}
              <svg class="fill-current h-5 w-5 ml-1" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path x-show="! dropdown" d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                <path x-show="dropdown" d="M10.707 7.05L10 6.343 4.343 12l1.414 1.414L10 9.172l4.243 4.242L15.657 12z"/>
              </svg>
            </button>

            <div class="absolute right-0 flex-col items-end z-10"
              :class="{ 'flex': dropdown, 'hidden': ! dropdown }"
              x-on:click.away="dropdown = false"
            >

              <div class="w-0 h-0 mr-8 z-20" style="border-left: 8px solid transparent; border-right: 8px solid transparent; border-bottom: 8px solid #ffffff;"></div>

              <div class="flex flex-col overflow-hidden bg-white rounded-lg shadow-2xl">
                <a
                  href="{{ route('settings') }}"
                  class="pl-5 pr-12 py-4 text-gray-800
                    hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-cool-gray-100
                    border-l-2 border-solid border-transparent
                    {{ $active == 'settings' ? 'border-blue-500' : 'hover:border-gray-400 focus:border-gray-400 active:border-blue-500' }}
                    focus:outline-none hover:no-underline
                    transition-colors duration-200"
                >
                  {{ __('misc.menu.settings') }}
                </a>

                <a
                  href="{{ route('logout') }}"
                  onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                  class="pl-5 pr-12 py-4 text-gray-800
                    hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-cool-gray-100
                    border-l-2 border-solid border-transparent
                    hover:border-gray-400 focus:border-gray-400 active:border-blue-500
                    focus:outline-none hover:no-underline
                    transition-colors duration-200"
                >
                  {{ __('misc.menu.logout') }}
                </a>
              </div>

            </div>
          </div>

          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none">
            @csrf
          </form>
        @endguest

        <form
          action="{{ route('locale.store') }}" method="POST"
          class="lg:mt-1 px-2 py-1 text-gray-800 text-sm flex items-center"
        >
          @csrf
          {{ __('misc.language') }}:&nbsp;
          <div>
            @unless (app()->isLocale('pl'))
              <button name="language" value="pl"
                class="btn-out leading-none text-xs rounded px-2"
              >
                PL
              </button>
            @endunless
            @unless (app()->isLocale('en'))
              <button name="language" value="en"
                class="btn-out leading-none text-xs rounded px-2"
              >
                EN
              </button>
            @endunless
            @unless (app()->isLocale('de'))
              <button name="language" value="de"
                class="btn-out leading-none text-xs rounded px-2"
              >
                DE
              </button>
            @endunless
          </div>

        </form>

      </div>

    </div>
  </div>
</nav>
