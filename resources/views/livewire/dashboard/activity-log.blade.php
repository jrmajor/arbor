@push('scripts')
    <livewire:styles>
    <livewire:scripts>
@endpush

@section('title-bar', __('Activity log'))

@section('sidebar-menu')
    <x-dashboard-menu active="activitylog"/>
@endsection

<div>
    <table>
        @foreach ($activities as $activity)
            <tr wire:key="{{ $activity->id }}">
                <td class="tnum text-right">
                    {{ $activity->created_at->format('Y-m-d h:s') }}
                </td>
                <td>
                    @if($activity->log_name == 'people')
                        <a href="{{ route('people.history', $activity->subject) }}" class="a">
                            {{ __('people.person').' №'.$activity->subject->id }}
                        </a>
                        @if($activity->description == 'changed-visibility')
                            @if($activity->properties['attributes']['visibility'])
                                {{ __('activities.made_visible') }}
                            @else
                                {{ __('activities.made_invisible') }}
                            @endif
                        @else
                            {{ __('activities.'.$activity['description']) }}
                        @endif
                        @if($activity->causer)
                            {{ __('activities.by') }} <strong>{{ $activity->causer->username }}</strong>
                        @endif
                    @elseif($activity->log_name == 'marriages')
                        <a href="{{ route('marriages.history', $activity->subject) }}" class="a">
                            {{ __('marriages.marriage').' №'.$activity->subject->id }}
                        </a>
                        (<a href="{{ route('people.show', $activity->subject->woman_id) }}" class="a">{{ strtolower(__('marriages.woman')) }}</a>,
                        <a href="{{ route('people.show', $activity->subject->man_id) }}" class="a">{{ strtolower(__('marriages.man')) }}</a>)
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
                    @endif
                </td>
            </tr>
        @endforeach
    </table>

    <br>

    {{ $activities->links() }}
</div>
