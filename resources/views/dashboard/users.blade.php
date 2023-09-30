@section('title', 'Users')

<div class="flex flex-col md:flex-row space-x-2 space-y-2">

  <main class="grow md:w-1/2 p-6 bg-white rounded-lg shadow">
    <table>
      <thead>
        <tr>
          <th class="pr-4 text-left">username</th>
          <th class="px-4 text-left">perm.</th>
          <th class="px-4 text-left">email</th>
          <th class="pl-4 text-right">latest login</th>
        </tr>
      </thead>

      <tbody>
        @foreach ($users as $user)
          <tr wire:key="{{ $user->id }}">
            <td class="pr-4 pt-2">
              {{ $user->username }}
            </td>

            <td class="px-4 pt-2">
              {{ match ($user->permissions) {
                0 => 'none',
                1 => 'read',
                2 => 'write',
                3 => 'view history',
                4 => 'admin',
              } }}
            </td>

            <td class="px-4 pt-2">
              {{ $user->email ?: 'no email' }}
            </td>

            <td class="pl-4 pt-2 tabular-nums text-right">
              {{ $user->latestLogin?->created_at?->format('Y-m-d h:s') ?: 'never' }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </main>

  <div class="shrink-0 p-1">
    <x-sidebar-menus.dashboard active="users"/>
  </div>

</div>
