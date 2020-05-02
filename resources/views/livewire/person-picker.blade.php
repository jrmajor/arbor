<div class="flex flex-wrap items-end mb-1">
    <label for="{{ $name }}_id" class="w-full sm:w-1/2 md:w-1/4 pr-1 mb-1">{{ $label }}</label>
    <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 flex flex-wrap">
        <div class="w-full sm:w-1/4 md:w-3/8 sm:pr-2 mb-1">
            <input
                type="text"
                id="{{ $name }}_search" name="{{ $name }}_search"
                autocomplete=off
                wire:model="search"
                wire:keyup="search">
            <small class="text-red-500">
                @error($name.'_id'){{ $message }}@enderror
            </small>
        </div>
        <div class="w-full sm:w-3/4 md:w-5/8 mb-1">
            <div class="inline-block relative w-full">
                <select id="{{ $name }}_id" name="{{ $name }}_id"
                    @if(empty($people)) disabled @endif>
                    @if(empty($people))
                        <option>←</option>
                    @else
                        @foreach($people as $person)
                            <option value="{{ $person['id'] }}">
                                {{ $person['name'] }}
                            </option>
                        @endforeach
                        @if($nullable)
                            <option></option>
                        @endif
                    @endif
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
