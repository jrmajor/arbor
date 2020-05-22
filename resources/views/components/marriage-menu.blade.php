<ul class="uppercase">
    <div class="flex flex-col xs:flex-row md:flex-col">
        <div class="flex-grow">

            <{{ $active == 'edit' ? 'span' : 'a' }}
                href="{{ route('marriages.edit', $marriage) }}"
                class="{{ $active == 'edit' ? 'text-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                <li class="px-3 py-1 rounded {{ $active != 'edit' ? 'hover:bg-gray-200' : '' }}">
                    <span class="{{ $active == 'edit' ? 'border-b-2 border-dotted border-blue-500 block' : '' }}">
                        {{ __('marriages.menu.edit_marriage') }}
                    </span>
                </li>
            </{{ $active == 'edit' ? 'span' : 'a' }}>

            @if(optional(auth()->user())->isSuperAdmin())
                <{{ $active == 'history' ? 'span' : 'a' }}
                    href="{{ route('marriages.history', $marriage) }}"
                    class="{{ $active == 'history' ? 'text-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                    <li class="px-3 py-1 rounded {{ $active != 'history' ? 'hover:bg-gray-200' : '' }}">
                        <span class="{{ $active == 'history' ? 'border-b-2 border-dotted border-blue-500 block' : '' }}">
                            {{ __('marriages.menu.edits_history') }}
                        </span>
                    </li>
                </{{ $active == 'history' ? 'span' : 'a' }}>
            @endif

            <a
                href="{{ route('marriages.destroy', $marriage) }}"
                onclick="event.preventDefault();document.getElementById('delete-marriage-form').submit();"
                class="text-red-500 hover:text-red-700">
                <li class="px-3 py-1 rounded hover:bg-gray-200">
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
                <li class="px-3 py-1 rounded hover:bg-gray-200">
                    <span>
                        {{ __('marriages.add_child') }}
                    </span>
                </li>
            </a>

            <hr class="my-1">

            <a
                href="{{ route('people.show', $marriage->woman) }}"
                class="text-gray-600 hover:text-gray-800">
                <li class="px-3 py-1 rounded hover:bg-gray-200">
                    <span>
                        {{ __('marriages.woman') }}
                    </span>
                </li>
            </a>

            <a
                href="{{ route('people.show', $marriage->man) }}"
                class="text-gray-600 hover:text-gray-800">
                <li class="px-3 py-1 rounded hover:bg-gray-200">
                    <span>
                        {{ __('marriages.man') }}
                    </span>
                </li>
            </a>

        </div>
    </div>
</ul>
