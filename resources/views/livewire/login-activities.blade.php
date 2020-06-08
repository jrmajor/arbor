@push('scripts')
    <livewire:styles>
    <livewire:scripts>
@endpush

@section('title-bar', __('activities.login-activities'))

<div>
    <input class="m-1" type="checkbox"
        id="showMine" wire:model="showMine">
    <label for="showMine">show yours</label>

    <ul>
        @foreach ($activities as $activity)
            <li wire:key="{{ $activity->id }}">
                <span class="tnum">
                    {{ $activity->created_at->format('Y-m-d h:s') }}
                </span>
                @if($activity->log_name == 'logins')
                    <strong>{{ optional($activity->causer)->username }}</strong>
                @endif
            </li>
        @endforeach
    </ul>

    <br>

    {{ $activities->links() }}
</div>
