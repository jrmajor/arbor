<ul class="uppercase">
    <div class="flex flex-col xs:flex-row md:flex-col">
        <div class="flex-grow">

            <{{ $active == 'edit' ? 'span' : 'a' }}
                href="{{ route('marriages.edit', $marriage) }}"
                class="{{ $active == 'edit' ? 'text-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                <li class="px-3 py-1 rounded {{ $active != 'edit' ? 'hover:bg-gray-200' : '' }}">
                    <span class="{{ $active == 'edit' ? 'border-b-2 border-dotted border-blue-500 block' : '' }} flex items-center">
                        <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M12.3 3.7l4 4L4 20H0v-4L12.3 3.7zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z"/>
                        </svg>
                        {{ __('marriages.menu.edit_marriage') }}
                    </span>
                </li>
            </{{ $active == 'edit' ? 'span' : 'a' }}>

            @if(optional(auth()->user())->isSuperAdmin())
                <{{ $active == 'history' ? 'span' : 'a' }}
                    href="{{ route('marriages.history', $marriage) }}"
                    class="{{ $active == 'history' ? 'text-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                    <li class="px-3 py-1 rounded {{ $active != 'history' ? 'hover:bg-gray-200' : '' }}">
                        <span class="{{ $active == 'history' ? 'border-b-2 border-dotted border-blue-500 block' : '' }} flex items-center">
                            <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-1-7.59V4h2v5.59l3.95 3.95-1.41 1.41L9 10.41z"/>
                            </svg>
                            {{ __('marriages.menu.edits_history') }}
                        </span>
                    </li>
                </{{ $active == 'history' ? 'span' : 'a' }}>
            @endif

            <a
                href="{{ route('marriages.destroy', $marriage) }}"
                onclick="event.preventDefault();document.getElementById('delete-marriage-form').submit();"
                class="text-red-500 hover:text-red-700">
                <li class="px-3 py-1 rounded hover:bg-gray-200 flex items-center">
                    <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/>
                    </svg>
                    {{ __('marriages.menu.delete') }}
                </li>
                <form id="delete-marriage-form" method="POST" style="display: none"
                    action="{{ route('marriages.destroy', $marriage) }}">
                    @method('DELETE')
                    @csrf
                </form>
            </a>

        </div>

        <div class="flex-grow">

            <hr class="my-1 block xs:hidden md:block">

            <a href="{{ route('people.create', [
                                    'mother' => $marriage->woman_id,
                                    'father' => $marriage->man_id,
                ]) }}"
                class="text-gray-600 hover:text-gray-800">
                <li class="px-3 py-1 rounded hover:bg-gray-200 flex items-center">
                    <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M11 9h4v2h-4v4H9v-4H5V9h4V5h2v4zm-1 11a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z"/>
                    </svg>
                    {{ __('marriages.add_child') }}
                </li>
            </a>

            <hr class="my-1">

            <a
                href="{{ route('people.show', $marriage->woman) }}"
                class="text-gray-600 hover:text-gray-800">
                <li class="px-3 py-1 rounded hover:bg-gray-200 flex items-center">
                    <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M.2 10a11 11 0 0 1 19.6 0A11 11 0 0 1 .2 10zm9.8 4a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0-2a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
                    </svg>
                    {{ __('marriages.woman') }}
                </li>
            </a>

            <a
                href="{{ route('people.show', $marriage->man) }}"
                class="text-gray-600 hover:text-gray-800">
                <li class="px-3 py-1 rounded hover:bg-gray-200 flex items-center">
                    <svg class="fill-current h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M.2 10a11 11 0 0 1 19.6 0A11 11 0 0 1 .2 10zm9.8 4a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0-2a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
                    </svg>
                    {{ __('marriages.man') }}
                </li>
            </a>

        </div>
    </div>
</ul>
