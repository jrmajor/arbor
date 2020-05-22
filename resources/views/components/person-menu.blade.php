<ul class="uppercase">
    <div class="flex flex-col xs:flex-row md:flex-col">
        <div class="flex-grow">

            <{{ $active == 'show' ? 'span' : 'a' }}
                href="{{ route('people.show', $person) }}"
                class="{{ $active == 'show' ? 'text-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                <li class="px-3 py-1 rounded {{ $active != 'show' ? 'hover:bg-gray-200' : '' }} flex items-center">
                    <span class="{{ $active == 'show' ? 'border-b-2 border-dotted border-blue-500 block' : '' }} flex items-center">
                        <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M.2 10a11 11 0 0 1 19.6 0A11 11 0 0 1 .2 10zm9.8 4a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0-2a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
                        </svg>
                        {{ __('people.menu.overview') }}
                    </span>
                </li>
            </{{ $active == 'show' ? 'span' : 'a' }}>

            <{{ $active == 'edit' ? 'span' : 'a' }}
                href="{{ route('people.edit', $person) }}"
                class="{{ $active == 'edit' ? 'text-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                <li class="px-3 py-1 rounded {{ $active != 'edit' ? 'hover:bg-gray-200' : '' }}">
                    <span class="{{ $active == 'edit' ? 'border-b-2 border-dotted border-blue-500 block' : '' }} flex items-center">
                        <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M12.3 3.7l4 4L4 20H0v-4L12.3 3.7zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z"/>
                        </svg>
                        {{ __('people.menu.edit_person') }}
                    </span>
                </li>
            </{{ $active == 'edit' ? 'span' : 'a' }}>

            @if(optional(auth()->user())->isSuperAdmin())
                <{{ $active == 'history' ? 'span' : 'a' }}
                    href="{{ route('people.history', $person) }}"
                    class="{{ $active == 'history' ? 'text-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                    <li class="px-3 py-1 rounded {{ $active != 'history' ? 'hover:bg-gray-200' : '' }}">
                        <span class="{{ $active == 'history' ? 'border-b-2 border-dotted border-blue-500 block' : '' }} flex items-center">
                            <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-1-7.59V4h2v5.59l3.95 3.95-1.41 1.41L9 10.41z"/>
                            </svg>
                            {{ __('people.menu.edits_history') }}
                        </span>
                    </li>
                </{{ $active == 'history' ? 'span' : 'a' }}>
            @endif

        </div>

        <div class="flex-grow">

            @if(optional(auth()->user())->isSuperAdmin())
                <a
                    href="{{ route('people.changeVisibility', $person) }}"
                    onclick="event.preventDefault();document.getElementById('change-visibility-form').submit();"
                    class="{{ ! $person->isVisible() ? 'text-red-500 hover:text-red-700' : 'text-gray-600 hover:text-gray-800' }}">
                    <li class="px-3 py-1 rounded hover:bg-gray-200 flex items-center">
                        @if($person->isVisible())
                            <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M17 16a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4.01V4a1 1 0 0 1 1-1 1 1 0 0 1 1 1v6h1V2a1 1 0 0 1 1-1 1 1 0 0 1 1 1v8h1V1a1 1 0 1 1 2 0v9h1V2a1 1 0 0 1 1-1 1 1 0 0 1 1 1v13h1V9a1 1 0 0 1 1-1h1v8z"/>
                            </svg>
                            {{ __('people.menu.make_invisible') }}
                        @else
                            <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm2-2.25a8 8 0 0 0 4-2.46V9a2 2 0 0 1-2-2V3.07a7.95 7.95 0 0 0-3-1V3a2 2 0 0 1-2 2v1a2 2 0 0 1-2 2v2h3a2 2 0 0 1 2 2v5.75zm-4 0V15a2 2 0 0 1-2-2v-1h-.5A1.5 1.5 0 0 1 4 10.5V8H2.25A8.01 8.01 0 0 0 8 17.75z"/>
                            </svg>
                            {{ __('people.menu.make_visible') }}
                        @endif
                    </li>
                    <form id="change-visibility-form" method="POST" style="display: none"
                        action="{{ route('people.changeVisibility', $person) }}">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="visibility" value="{{ $person->isVisible() ? '0' : '1' }}">
                    </form>
                </a>
            @endif

            <a
                href="{{ route('people.destroy', $person) }}"
                onclick="event.preventDefault();document.getElementById('delete-person-form').submit();"
                class="text-red-500 hover:text-red-700">
                <li class="px-3 py-1 rounded hover:bg-gray-200 flex items-center">
                    <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/>
                    </svg>
                    {{ __('people.menu.delete') }}
                </li>
                <form id="delete-person-form" method="POST" style="display: none"
                    action="{{ route('people.destroy', $person) }}">
                    @method('DELETE')
                    @csrf
                </form>
            </a>

            <hr class="my-1">

            <a
                href="{{ route('marriages.create', [$person->sex == 'xx' ? 'woman' : 'man' => $person->id]) }}"
                class="text-gray-600 hover:text-gray-800">
                <li class="px-3 py-1 rounded hover:bg-gray-200 flex items-center">
                    <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M11 9h4v2h-4v4H9v-4H5V9h4V5h2v4zm-1 11a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z"/>
                    </svg>
                    {{ __('marriages.add_a_new_marriage') }}
                </li>
            </a>

        </div>
    </div>
</ul>
