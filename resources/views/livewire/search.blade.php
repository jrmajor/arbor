@push('scripts')
    <livewire:styles>
    <livewire:scripts>
@endpush

<div>
    <form class="relative" wire:submit.prevent="$refresh">
        <input type="search" wire:model.debounce.500ms="s" autocomplete="off" class="h-10"></input>
        <button type="submit"
            class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 hover:text-gray-900 transition-colors duration-200 ease-out">
            <svg class="fill-current h-5 w-5" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/>
            </svg>
        </button>
    </form>

@if(! blank($s))
    <hr class="-mx-4 my-3 border-t-2 border-dashed">

    @if($people->isNotEmpty())
        <ul>
            @foreach($people as $person)
                <li wire:key="{{ $person->id }}">
                    <x-name :person="$person"/>
                </li>
            @endforeach
        </ul>

        @if($people->hasPages())
            <div class="mt-2">
                {{ $people->links() }}
            </div>
        @endif
    @else
        <p>{{ __('misc.no_results') }}</p>
    @endif
@endif
</div>
