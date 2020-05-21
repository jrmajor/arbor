<ul class="uppercase">
    <{{ $active == 'show' ? 'span' : 'a' }}
        href="{{ route('people.show', $person) }}"
        class="{{ $active == 'show' ? 'text-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
        <li class="px-3 py-1 rounded {{ $active != 'show' ? 'hover:bg-gray-200' : '' }}">
            <span class="{{ $active == 'show' ? 'border-b-2 border-dotted border-blue-500 block' : '' }}">
                {{ __('people.menu.overview') }}
            </span>
        </li>
    </{{ $active == 'show' ? 'span' : 'a' }}>

    <{{ $active == 'edit' ? 'span' : 'a' }}
        href="{{ route('people.edit', $person) }}"
        class="{{ $active == 'edit' ? 'text-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
        <li class="px-3 py-1 rounded {{ $active != 'edit' ? 'hover:bg-gray-200' : '' }}">
            <span class="{{ $active == 'edit' ? 'border-b-2 border-dotted border-blue-500 block' : '' }}">
                {{ __('people.menu.edit_person') }}
            </span>
        </li>
    </{{ $active == 'edit' ? 'span' : 'a' }}>

    @if(optional(auth()->user())->isSuperAdmin())
        <{{ $active == 'history' ? 'span' : 'a' }}
            href="{{ route('people.history', $person) }}"
            class="{{ $active == 'history' ? 'text-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
            <li class="px-3 py-1 rounded {{ $active != 'history' ? 'hover:bg-gray-200' : '' }}">
                <span class="{{ $active == 'history' ? 'border-b-2 border-dotted border-blue-500 block' : '' }}">
                    {{ __('people.menu.edits_history') }}
                </span>
            </li>
        </{{ $active == 'history' ? 'span' : 'a' }}>

        <a
            href="{{ route('people.changeVisibility', $person) }}"
            onclick="event.preventDefault();document.getElementById('change-visibility-form').submit();"
            class="{{ ! $person->isVisible() ? 'text-red-500 hover:text-red-700' : 'text-gray-600 hover:text-gray-800' }}">
            <li class="px-3 py-1 rounded hover:bg-gray-200">
                {{ $person->isVisible() ? __('people.menu.make_invisible') : __('people.menu.make_visible') }}
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
        <li class="px-3 py-1 rounded hover:bg-gray-200">
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
        <li class="px-3 py-1 rounded hover:bg-gray-200">
            <span>
                {{ __('marriages.add_a_new_marriage') }}
            </span>
        </li>
    </a>
</ul>
