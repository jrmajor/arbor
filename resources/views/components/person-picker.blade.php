@props(['name', 'label', 'sex', 'nullable', 'initial'])

<div {{ $attributes->merge(['class' => 'flex flex-col']) }}
    x-data="personPickerData(
        @encodedjson([
            'route' => route('people.picker'),
            'nullable' => $nullable,
            'sex' => $sex,
            'initial' => [
                'id' => optional($initial)->id,
                'name' => optional($initial)->formatName(),
            ],
        ])
    )">
    <label for="{{ $name }}_search" class="w-full font-medium pb-1 text-gray-700">{{ $label }}</label>
    <div class="w-full">
        <input
            type="hidden"
            name="{{ $name }}_id"
            x-model="selected.id">
        <div class="relative w-full"
            x-on:mousedown.away="closeDropdown()">
            <div class="relative block cursor-text form-select
                    @error($name.'_id') shadow-outline-red @else active:shadow-outline-blue focus-within:shadow-outline-blue @enderror"
                x-on:click="$refs.search.focus()">
                <div class="pr-4">
                    <span x-text="selected.name">
                    </span>{{--
                    --}}<input
                        type="text" class="appearance-none outline-none text-gray-600 focus:text-gray-800"
                        :style="selected.id != null ? 'width: 4px' : 'width: 100%'" autocomplete="off"
                        x-ref="search" x-model="search" id="{{ $name }}_search"
                        x-on:focus="open = true" x-on:input="findPeople($event)">
                </div>
            </div>
            <template x-if="open">
                <div class="absolute mt-2 z-50 py-1 w-full text-gray-800 bg-white rounded-md shadow-md border border-gray-300">
                    <template x-if="people.length == 0">
                        <div class="w-full px-3 py-1 text-gray-600">
                            {{ __('misc.no_results') }}
                        </div>
                    </template>
                    <template x-for="person in people" x-key="person.id">
                        <button
                            x-on:click.prevent="selectPerson(person)"
                            class="flex w-full px-3 py-1 text-gray-800 text-left justify-between hover:bg-cool-gray-100">
                            <span x-text="person.name"></span>
                            <span class="text-gray-400" x-text="selected.id == person.id ? '✓ ' : ''"></span>
                        </button>
                    </template>
                </div>
            </template>
        </div>
        @error($name.'_id')
            <small class="text-red-500">{{ $message }}</small>
        @enderror
    </div>
</div>
