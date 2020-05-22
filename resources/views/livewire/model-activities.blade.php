@push('scripts')
    <livewire:styles>
    <livewire:scripts>
@endpush

@section('title-bar')
    {{ __('activities.model-activities') }}
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
                        <a href="{{ route('people.history', $activity->subject) }}">
                            {{ __('people.person').' №'.$activity->subject->id }}
                        </a>
                        {{ __('activities.'.$activity['description']) }}
                        @if($activity->causer)
                            {{ __('activities.by') }} <strong>{{ $activity->causer->username }}</strong>
                        @endif
                    @elseif($activity->log_name == 'marriages')
                        <a href="{{ route('marriages.history', $activity->subject) }}">
                            {{ __('marriages.marriage').' №'.$activity->subject->id }}
                        </a>
                        (<a href="{{ route('people.show', $activity->subject->woman_id) }}">{{ strtolower(__('marriages.woman')) }}</a>,
                        <a href="{{ route('people.show', $activity->subject->man_id) }}">{{ strtolower(__('marriages.man')) }}</a>)
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
