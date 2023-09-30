@section('title', 'Activity log')

<div class="flex flex-col md:flex-row space-x-2 space-y-2">

  <main class="grow md:w-1/2 space-y-2 flex flex-col items-center">

    <div class="w-full p-6 bg-white rounded-lg shadow">
      <table>
        @foreach ($activities as $activity)
          <tr wire:key="{{ $activity->id }}">

            <td class="tabular-nums p-1">
              {{ $activity->created_at->format('Y-m-d h:s') }}
            </td>

            <td class="p-1">
              @if ($activity->log_name === 'people')

                <a href="{{ route('people.history', $activity->subject) }}" class="a">
                  {{ __('people.person').' №'.$activity->subject->id }}
                </a>

                @if ($activity->description === 'changed-visibility')
                  {{ $activity->properties['attributes']['visibility'] ? __('activities.made_visible') : __('activities.made_invisible') }}
                @else
                  {{ __('activities.'.$activity['description']) }}
                @endif

                @if ($activity->causer)
                  {{ __('activities.by') }} <strong>{{ $activity->causer->username }}</strong>
                @endif

              @elseif ($activity->log_name === 'marriages')

                <a href="{{ route('marriages.history', $activity->subject) }}" class="a">
                  {{ __('marriages.marriage').' №'.$activity->subject->id }}
                </a>

                (<a href="{{ route('people.show', $activity->subject->woman_id) }}" class="a">{{ strtolower(__('marriages.woman')) }}</a>,
                <a href="{{ route('people.show', $activity->subject->man_id) }}" class="a">{{ strtolower(__('marriages.man')) }}</a>)

                {{ __('activities.'.$activity['description']) }}

                @if ($activity->causer)
                  {{ __('activities.by') }} <strong>{{ $activity->causer->username }}</strong>
                @endif

              @elseif ($activity->log_name === 'users')

                {{ __('users.user').' №'.$activity->subject->id }}
                {{ __('activities.'.$activity['description']) }}

                @if ($activity->causer)
                  {{ __('activities.by') }} <strong>{{ $activity->causer->username }}</strong>
                @endif

              @endif
            </td>

          </tr>
        @endforeach
      </table>
    </div>

    {{ $activities->links() }}

  </main>

  <div class="shrink-0 p-1">
    <x-sidebar-menus.dashboard active="activityLog"/>
  </div>

</div>
