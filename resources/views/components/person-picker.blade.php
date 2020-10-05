@props(['name', 'label', 'sex', 'nullable', 'initial'])

<div {{ $attributes->merge(['class' => 'flex flex-col']) }}
    x-data="personPickerData(
        @encodedjson([
            'route' => route('people.search'),
            'nullable' => $nullable,
            'sex' => $sex,
            'initial' => [
                'id' => optional($initial)->id,
                'name' => optional($initial)->formatSimpleName(),
                'dates' => optional($initial)->formatSimpleDates(),
                'url' => $initial ? route('people.show', $initial) : null,
                'hidden' => false,
            ],
        ])
    )" x-init="init()">
    <label for="{{ $name }}_search" class="w-full font-medium pb-1 text-gray-700">{{ $label }}</label>
    <div class="w-full">
        <input
            type="hidden"
            name="{{ $name }}_id"
            x-model="selected.id">
        <div class="relative w-full">
            <div x-on:click="$refs.search.focus()" class="block cursor-text form-select
                    @error($name.'_id') shadow-outline-red @else active:shadow-outline-blue focus-within:shadow-outline-blue @enderror">
                <div class="pr-4">
                    <span x-text="selected.name">
                    </span>{{--
                --}}<input
                        type="text" class="appearance-none outline-none text-gray-600 focus:text-gray-800"
                        :style="selected.id !== null ? 'width: 4px' : 'width: 100%'" autocomplete="off"
                        x-ref="search" x-model="search" id="{{ $name }}_search"
                        x-on:keydown.backspace="deselect" x-on:keydown.enter.prevent="enter"
                        x-on:keydown.arrow-up="arrow('up')" x-on:keydown.arrow-down="arrow('down')"
                        x-on:keydown="keydown" x-on:input="findPeople"
                        x-on:focus="open = shouldCloseOnBlur = true" x-on:blur="closeDropdown">
                </div>
            </div>
            <template x-if="open && ! (search === '' && people.length === 0)">
                <ul class="absolute mt-2 z-50 py-1 w-full text-gray-800 bg-white rounded-md shadow-md border border-gray-300"
                    x-on:mousedown="shouldCloseOnBlur = false">
                    <template x-if="people.length === 0">
                        <li class="w-full px-3 py-1 text-gray-600">
                            {{ __('misc.no_results') }}
                        </li>
                    </template>
                    <template x-for="(person, index) in people" x-key="person.id">
                        <li
                            x-on:mouseover="hovered = index" x-on:click="selectPerson(person)"
                            class="select-none w-full px-3 py-1 text-gray-800 flex justify-between items-center"
                            :class="{ 'bg-cool-gray-100': hovered === index }">
                            <span>
                                <span x-text="person.name"></span>
                                <small x-text="'[№' + person.id + ']'"></small>
                            </span>
                            <span class="text-gray-800 font-bold" x-text="selected.id === person.id ? '✓ ' : ''"></span>
                        </li>
                    </template>
                </ul>
            </template>
        </div>
        @error($name.'_id')
            <small class="text-red-500">{{ $message }}</small>
        @enderror
    </div>
</div>
