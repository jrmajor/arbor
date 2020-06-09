@push('scripts')
    <livewire:styles>
    <livewire:scripts>
@endpush

<div class="flex flex-col md:flex-row space-x-2 space-y-2">

    <div class="flex-grow p-4 bg-white rounded-lg shadow-lg">
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

    <div class="flex-shrink-0 p-1">
        <x-dashboard-menu active="users"/>
    </div>

</div>
