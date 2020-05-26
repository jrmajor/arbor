<div class="flex flex-wrap">
    <label for="id_pytlewski" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">
        {!! __('people.pytlewski.id') !!}
    </label>
    <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2">
        <div class="w-full flex flex-wrap sm:flex-no-wrap space-y-2 sm:space-y-0 sm:space-x-2">
            <div class="w-full sm:w-1/4 md:w-3/8">
                <input
                    type="text" class="@error('id_pytlewski') invalid @enderror"
                    id="id_pytlewski" name="id_pytlewski"
                    autocomplete=off
                    wire:model="pytlewskiId"
                    wire:keyup="search">
            </div>
            <div class="w-full sm:w-3/4 md:w-5/8">
                <input
                    type="text"
                    wire:model="result"
                    disabled>
            </div>
        </div>
        @error('id_pytlewski')
            <small class="text-red-500">{{ $message }}</small>
        @enderror
    </div>
</div>
