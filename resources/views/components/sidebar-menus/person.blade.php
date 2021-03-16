<ul class="uppercase">
    <div class="flex flex-col xs:flex-row md:flex-col">

        <div class="flex-grow">

            @unless($person->trashed())
                <x-sidebar-menus.item
                    :active="$active === 'show'"
                    :route="route('people.show', $person)"
                    name="people.menu.overview">
                    <path d="M.2 10a11 11 0 0 1 19.6 0A11 11 0 0 1 .2 10zm9.8 4a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0-2a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
                </x-sidebar-menus.item>

                <x-sidebar-menus.item
                    :active="$active === 'edit'"
                    :route="route('people.edit', $person)"
                    name="people.menu.edit_person">
                    <path d="M12.3 3.7l4 4L4 20H0v-4L12.3 3.7zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z"/>
                </x-sidebar-menus.item>

                <x-sidebar-menus.item
                    :active="$active === 'biography'"
                    :route="route('people.biography.edit', $person)"
                    name="people.menu.edit_biography">
                    <path d="M12.3 3.7l4 4L4 20H0v-4L12.3 3.7zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z"/>
                </x-sidebar-menus.item>
            @endif

            @can('viewHistory', $person)
                <x-sidebar-menus.item
                    :active="$active === 'history'"
                    :route="route('people.history', $person)"
                    name="people.menu.edits_history">
                    <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-1-7.59V4h2v5.59l3.95 3.95-1.41 1.41L9 10.41z"/>
                </x-sidebar-menus.item>
            @endif

        </div>

        <div class="flex-grow">

            @if(auth()->user()->can('changeVisibility', $person) && ! $person->trashed())
                <x-sidebar-menus.item
                    :danger="! $person->isVisible()"
                    :route="route('people.changeVisibility', $person)"
                    :name="$person->isVisible() ? 'people.menu.make_invisible' : 'people.menu.make_visible'"
                    :form="[
                        'name' => 'change-visibility',
                        'method' => 'PUT',
                    ]">
                    <x-slot name="formBody">
                        <input type="hidden" name="visibility" value="{{ $person->isVisible() ? '0' : '1' }}">
                    </x-slot>

                    @if($person->isVisible())
                        <path d="M17 16a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4.01V4a1 1 0 0 1 1-1 1 1 0 0 1 1 1v6h1V2a1 1 0 0 1 1-1 1 1 0 0 1 1 1v8h1V1a1 1 0 1 1 2 0v9h1V2a1 1 0 0 1 1-1 1 1 0 0 1 1 1v13h1V9a1 1 0 0 1 1-1h1v8z"/>
                    @else
                        <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm2-2.25a8 8 0 0 0 4-2.46V9a2 2 0 0 1-2-2V3.07a7.95 7.95 0 0 0-3-1V3a2 2 0 0 1-2 2v1a2 2 0 0 1-2 2v2h3a2 2 0 0 1 2 2v5.75zm-4 0V15a2 2 0 0 1-2-2v-1h-.5A1.5 1.5 0 0 1 4 10.5V8H2.25A8.01 8.01 0 0 0 8 17.75z"/>
                    @endif
                </x-sidebar-menus.item>
            @endif

            @unless($person->trashed())
                <x-sidebar-menus.item danger
                    :route="route('people.destroy', $person)"
                    name="people.menu.delete"
                    :form="[
                        'name' => 'delete-person',
                        'method' => 'DELETE',
                    ]">
                    <path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/>
                </x-sidebar-menus.item>
            @elsecan('viewHistory', $person)
                <x-sidebar-menus.item danger
                    :route="route('people.restore', $person)"
                    name="people.menu.restore"
                    :form="[
                        'name' => 'restore-person',
                        'method' => 'PATCH',
                    ]">
                    <path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/>
                </x-sidebar-menus.item>
            @endif

            @unless($person->trashed())
                <hr class="my-1">

                <x-sidebar-menus.item
                    :route="route('marriages.create', [
                        $person->sex === 'xx' ? 'woman' : 'man' => $person->id,
                    ])"
                    name="marriages.add_a_new_marriage">
                    <path d="M11 9h4v2h-4v4H9v-4H5V9h4V5h2v4zm-1 11a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z"/>
                </x-sidebar-menus.item>
            @endif

        </div>

    </div>
</ul>
