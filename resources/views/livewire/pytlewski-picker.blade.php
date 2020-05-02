<div class="flex flex-wrap items-end mb-1">
    <label for="id_pytlewski" class="w-full sm:w-1/2 md:w-1/4 pr-1">
        {!! __('people.pytlewski.id') !!}
    </label>
    <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 flex flex-wrap">
        <div class="w-full sm:w-1/4 md:w-3/8 sm:pr-2 mb-1">
            <input
                type="text"
                id="id_pytlewski" name="id_pytlewski"
                autocomplete=off
                wire:model="pytlewskiId"
                wire:keyup="search">
            <small class="text-red-500">
                @error('id_pytlewski'){{ $message }}@enderror
            </small>
        </div>
        <div class="w-full sm:w-3/4 md:w-5/8 mb-1">
            <input
                type="text"
                wire:model="result"
                disabled>
        </div>
    </div>
</div>
