@push('scripts')
    <livewire:styles>
    <livewire:scripts>
@endpush

@section('title-bar', 'Users')

@section('sidebar-menu')
    <x-dashboard-menu active="users"/>
@endsection

<div>
    <table>
        @foreach ($users as $user)
            <tr wire:key="{{ $user->id }}">
                <td>
                    <strong>{{ $user->username }}</strong>
                </td>
                <td class="tnum">
                    {{ optional(optional($user->latestLogin)->created_at)->format('Y-m-d h:s') }}
                </td>
            </tr>
        @endforeach
    </table>
</div>
