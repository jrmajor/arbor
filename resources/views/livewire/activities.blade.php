@push('scripts')
    <livewire:styles>
    <livewire:scripts>
@endpush

<div>
    <input class="m-1" type="checkbox"
        id="filterLogins" wire:model="filterLogins">
    <label for="filterLogins">Logins</label>
    <input class="m-1" type="checkbox"
        id="filterUsers" wire:model="filterUsers">
    <label for="filterUsers">Users</label>
    <br>
    <input class="m-1" type="checkbox"
        id="filterEditions" wire:model="filterEditions">
    <label for="filterEditions">People / Marriages</label>

    <table>
        @foreach ($activities as $activity)
            <tr wire:key="{{ $activity->id }}">
                <td class="tnum text-right">
                    {{ $activity->created_at->format('Y-m-d h:s') }}
                </td>
                <td>
                    @if($activity->log_name == 'people')
                        <a href="{{ route('people.show', [$activity->subject]) }}">
                            {{ __('people.person').' №'.$activity->subject->id }}
                        </a>
                        {{ __('activities.'.$activity['description']) }}
                        @if($activity->causer)
                            {{ __('activities.by') }} <strong>{{ $activity->causer->username }}</strong>
                        @endif
                    @elseif($activity->log_name == 'marriages')
                        {{ __('marriages.marriage').' №'.$activity->subject->id }}
                        (<a href="{{ route('people.show', [$activity->subject]) }}">{{ strtolower(__('marriages.woman')) }}</a>,
                        <a href="{{ route('people.show', [$activity->subject]) }}">{{ strtolower(__('marriages.man')) }}</a>)
                        {{ __('activities.'.$activity['description']) }}
                        @if($activity->causer)
                            {{ __('activities.by') }} <strong>{{ $activity->causer->username }}</strong>
                        @endif
                    @elseif($activity->log_name == 'users')
                        {{ __('users.user').' №'.$activity->subject->id }}
                        {{ __('activities.'.$activity['description']) }}
                        @if($activity->causer)
                            {{ __('activities.by') }} <strong>{{ $activity->causer->username }}</strong>
                        @endif
                    @elseif($activity->log_name == 'logins')
                        <strong>{{ optional($activity->causer)->username }}</strong>
                        {{ __('activities.logged_in') }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>

    <br>

    {{ $activities->links() }}
</div>
