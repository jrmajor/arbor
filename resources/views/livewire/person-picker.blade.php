<div class="flex flex-wrap">
    <label for="{{ $name }}_id" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ $label }}</label>
    <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 flex flex-wrap sm:flex-no-wrap space-y-2 sm:space-y-0 sm:space-x-2">
        <div class="w-full sm:w-1/4 md:w-3/8">
            <input
                type="text"
                id="{{ $name }}_search" name="{{ $name }}_search"
                autocomplete="off"
                wire:model="search">
        </div>
        <div class="w-full sm:w-3/4 md:w-5/8">
            <div class="inline-block relative w-full">
                <select id="{{ $name }}_id" name="{{ $name }}_id">
                    @if(empty($people))
                        <option value=""></option>
                    @else
                        @foreach($people as $person)
                            <option value="{{ $person->id }}">
                                {{ $person->formatName() }}
                            </option>
                        @endforeach
                        @if($nullable)
                            <option value=""></option>
                        @endif
                    @endif
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
