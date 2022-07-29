<ul class="uppercase">
  <div class="flex flex-col xs:flex-row md:flex-col">

    <div class="grow">

      @unless ($marriage->trashed())
        <x-sidebar-menus.item
          :active="$active === 'edit'"
          :route="route('marriages.edit', $marriage)"
          name="marriages.menu.edit_marriage"
        >
          <path d="M12.3 3.7l4 4L4 20H0v-4L12.3 3.7zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z"/>
        </x-sidebar-menus.item>
      @endif

      @can('viewHistory', $marriage)
        <x-sidebar-menus.item
          :active="$active === 'history'"
          :route="route('marriages.history', $marriage)"
          name="marriages.menu.edits_history"
        >
          <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-1-7.59V4h2v5.59l3.95 3.95-1.41 1.41L9 10.41z"/>
        </x-sidebar-menus.item>
      @endif

      @unless ($marriage->trashed())
        <x-sidebar-menus.item danger
          :route="route('marriages.destroy', $marriage)"
          name="marriages.menu.delete"
          :form="[
            'name' => 'deleteMarriage',
            'method' => 'DELETE',
            'confirm' => __('marriages.menu.delete_confirm'),
          ]"
        >
          <path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/>
        </x-sidebar-menus.item>
      @elsecan('viewHistory', $marriage)
        <x-sidebar-menus.item danger
          :route="route('marriages.restore', $marriage)"
          name="marriages.menu.restore"
          :form="[
            'name' => 'restoreMarriage',
            'method' => 'PATCH',
          ]"
        >
          <path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/>
        </x-sidebar-menus.item>
      @endif

    </div>

    <div class="grow">

      <hr class="my-1 block xs:hidden md:block">

      <x-sidebar-menus.item
        :route="route('people.create', [
          'mother' => $marriage->woman_id,
          'father' => $marriage->man_id,
        ])"
        name="marriages.add_child"
      >
        <path d="M11 9h4v2h-4v4H9v-4H5V9h4V5h2v4zm-1 11a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z"/>
      </x-sidebar-menus.item>

      <hr class="my-1">

      <x-sidebar-menus.item
        :route="route('people.show', $marriage->woman)"
        name="marriages.woman"
      >
        <path d="M.2 10a11 11 0 0 1 19.6 0A11 11 0 0 1 .2 10zm9.8 4a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0-2a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
      </x-sidebar-menus.item>

      <x-sidebar-menus.item
        :route="route('people.show', $marriage->man)"
        name="marriages.man"
      >
        <path d="M.2 10a11 11 0 0 1 19.6 0A11 11 0 0 1 .2 10zm9.8 4a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0-2a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
      </x-sidebar-menus.item>

    </div>

  </div>
</ul>
