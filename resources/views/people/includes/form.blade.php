@push('scripts')
    @livewireStyles
    @livewireScripts
@endpush

<form
    method="POST"
    action="{{ $action == 'create' ? route('people.store') : route('people.update', $person) }}"
    x-data="
        @encodedjson([
            'sex' => old('sex', $person->sex),
            'dead' => old('dead', $person->dead),
            'sources' => [
                ...collect(old('sources', $person->sources))
                    ->map(fn ($source) => $source instanceof App\Source ? $source->raw() : $source)
            ],
        ])
    ">
    @method($action == 'create' ? 'post' : 'put')
    @csrf

    <div>
        <fieldset class="space-y-5">
            <div class="flex flex-col">
                <legend class="w-full font-medium pb-1 text-gray-700">{{ __('people.sex') }}</legend>
                <div class="w-full">
                    <div class="flex flex-col sm:flex-row sm:space-x-6">
                        <div class="w-full sm:w-auto flex items-center">
                            <input
                                type="radio" class="form-radio"
                                id="sex_1" name="sex"
                                value="xx" {{ (old('sex', $person->sex)) == 'xx' ? 'checked' : '' }}
                                x-model="sex">
                            <label class="ml-2" for="sex_1">{{ __('people.female') }}</label>
                        </div>
                        <div class="w-full sm:w-auto flex items-center">
                            <input
                                type="radio" class="form-radio"
                                id="sex_2" name="sex"
                                value="xy" {{ (old('sex', $person->sex)) == 'xy' ? 'checked' : '' }}
                                x-model="sex">
                            <label class="ml-2" for="sex_2">{{ __('people.male') }}</label>
                        </div>
                        <div class="w-full sm:w-auto flex items-center">
                            <input
                                type="radio" class="form-radio"
                                id="sex_3" name="sex"
                                value="" {{ (old('sex', $person->sex)) == null ? 'checked' : '' }}
                                x-model="sex">
                            <label class="ml-2" for="sex_3">{{ __('people.unknown') }}</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-5 lg:space-y-0 lg:space-x-5 flex flex-col lg:flex-row">
                <div class="space-y-5 sm:space-y-0 sm:space-x-5 w-full lg:w-1/2 flex flex-col sm:flex-row">
                    <div class="w-full sm:w-1/2 flex flex-col">
                        <label for="name" class="w-full font-medium pb-1 text-gray-700">{{ __('people.name') }}</label>
                        <div class="w-full">
                            <input
                                type="text" class="form-input w-full @error('name') invalid @enderror"
                                id="name" name="name"
                                value="{{ old('name', $person->name) }}">
                            @error('name')
                                <div class="w-full leading-none mt-1">
                                    <small class="text-red-500">{{ $message }}</small>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="w-full sm:w-1/2 flex flex-col">
                        <label for="middle_name" class="w-full font-medium pb-1 text-gray-700">{{ __('people.middle_name') }}</label>
                        <div class="w-full">
                            <input
                                type="text" class="form-input w-full @error('middle_name') invalid @enderror"
                                id="middle_name" name="middle_name"
                                value="{{ old('middle_name', $person->middle_name) }}">
                            @error('middle_name')
                                <div class="w-full leading-none mt-1">
                                    <small class="text-red-500">{{ $message }}</small>
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="space-y-5 sm:space-y-0 sm:space-x-5 w-full lg:w-1/2 flex flex-col sm:flex-row">
                    <div class="w-full sm:w-1/2 flex flex-col">
                        <label for="family_name" class="w-full font-medium pb-1 text-gray-700">{{ __('people.family_name') }}</label>
                        <div class="w-full">
                            <input
                                type="text" class="form-input w-full @error('family_name') invalid @enderror"
                                id="family_name" name="family_name"
                                value="{{ old('family_name', $person->family_name) }}">
                            @error('family_name')
                                <div class="w-full leading-none mt-1">
                                    <small class="text-red-500">{{ $message }}</small>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="w-full sm:w-1/2 flex flex-col">
                        <label for="last_name" class="w-full font-medium pb-1 text-gray-700">{{ __('people.last_name') }}</label>
                        <div class="w-full">
                            <input
                                type="text" class="form-input w-full @error('last_name') invalid @enderror"
                                id="last_name" name="last_name"
                                value="{{ old('last_name', $person->last_name) }}">
                            @error('last_name')
                                <div class="w-full leading-none mt-1">
                                    <small class="text-red-500">{{ $message }}</small>
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <hr class="mt-7 mb-6">

        <div class="w-full mb-4">
            <div class="font-medium text-xl text-gray-900">{{ __('people.external_links') }}</div>
        </div>
        <fieldset>
            <!-- https://bugs.chromium.org/p/chromium/issues/detail?id=375693 -->
            <div class="space-y-5 md:space-y-0 md:space-x-5 flex flex-col md:flex-row">
                <div class="w-full md:w-1/2 flex flex-col">
                    <label for="id_wielcy" class="w-full font-medium pb-1 text-gray-700">{!! __('people.wielcy.id') !!}</label>
                    <div class="w-full flex">
                        <input
                            type="text" class="form-input rounded-r-none w-1/4 md:w-3/8 z-10 @error('id_wielcy') invalid @enderror"
                            id="id_wielcy" name="id_wielcy"
                            value="{{ old('id_wielcy', $person->id_wielcy) }}">
                        <input
                            type="text" class="form-input rounded-l-none -ml-px w-3/4 md:w-5/8"
                            id="wielcy_search" name="wielcy_search"
                            placeholder="{{ __('misc.coming_soon') }}"
                            disabled>
                    </div>
                    @error('id_wielcy')
                        <div class="w-full leading-none mt-1">
                            <small class="text-red-500">{{ $message }}</small>
                        </div>
                    @enderror
                </div>

                <div class="w-full md:w-1/2">
                    <livewire:pytlewski-picker :person="$person">
                </div>
            </div>
        </fieldset>

        <hr class="mt-7 mb-6">

        {{--
        <div class="flex flex-wrap mb-2">
            <label class="w-full font-medium pb-1 text-gray-700">{{ __('people.pytlewski.guess') }}</label>
            <div class="w-full flex flex-wrap">
                <div class="w-1/3">
                    <button
                        type="button" class="w-full"
                        id="pytlewski_names"
                        onclick="pytlewskiNames()"
                        {{ $person->id_pytlewski == null ? 'disabled' : '' }}>{{ __('people.pytlewski.names') }}</button>
                </div>
                <div class="w-1/3 px-2">
                    <button
                        type="button" class="w-full"
                        id="pytlewski_birth"
                        onclick="pytlewskiBirth()"
                        {{ $person->id_pytlewski == null ? 'disabled' : '' }}>{{ __('people.pytlewski.birth') }}</button>
                </div>
                <div class="w-1/3">
                    <button
                        type="button" class="w-full"
                        id="pytlewski_death"
                        onclick="pytlewskiDeath()"
                        {{ $person->id_pytlewski == null ? 'disabled' : '' }}>{{ __('people.pytlewski.death') }}</button>
                </div>
            </div>
        </div>
        --}}

        <div class="w-full mb-4">
            <div class="font-medium text-xl text-gray-900">{{ __('people.birth') }}</div>
        </div>
        <fieldset>
            <!-- https://bugs.chromium.org/p/chromium/issues/detail?id=375693 -->
            <div class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
                <div class="w-full sm:w-1/2 flex flex-col">
                    <label for="birth_place" class="w-full font-medium pb-1 text-gray-700">{{ __('misc.place') }}</label>
                    <div class="w-full">
                        <input
                            type="text" class="form-input w-full @error('birth_place') invalid @enderror"
                            id="birth_place" name="birth_place"
                            value="{{ old('birth_place', $person->birth_place) }}">
                        @error('birth_place')
                            <div class="w-full leading-none mt-1">
                                <small class="text-red-500">{{ $message }}</small>
                            </div>
                        @enderror
                    </div>
                </div>
                <x-date-tuple-picker
                    class="w-full sm:w-1/2"
                    name="birth_date" :label="__('misc.date.date')"
                    :initial-from="old('birth_date_from', $person->birth_date_from)"
                    :initial-to="old('birth_date_to', $person->birth_date_to)"/>
            </div>
        </fieldset>

        <hr class="mt-7 mb-6">

        <div class="w-full flex items-center mb-4">
            <label for="dead" class="font-medium text-xl text-gray-900"
                x-text="sex == 'xx' ? '{{ __('people.dead_xx') }}' : '{{ __('people.dead_xy') }}'">
                {{ __('people.dead') }}
            </label>
            <input type="hidden" id="dead-hidden" name="dead" value="0">
            <input
                type="checkbox" class="form-checkbox ml-2 h-4 w-4"
                id="dead" name="dead"
                value="1"
                x-model="dead">
        </div>

        <fieldset class="space-y-5 x-show="dead">
            <div class="w-full">
                <label for="death_cause" class="w-full font-medium pb-1 text-gray-700">{{ __('people.death_cause') }}</label>
                <div class="w-full">
                    <input
                        type="text" class="form-input w-full sm:w-2/3 lg:w-1/3 @error('death_cause') invalid @enderror"
                        id="death_cause" name="death_cause"
                        value="{{ old('death_cause', $person->death_cause) }}">
                </div>
                @error('death_cause')
                    <div class="w-full leading-none mt-1">
                        <small class="text-red-500">{{ $message }}</small>
                    </div>
                @enderror
            </div>

            <div class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
                <div class="w-full sm:w-1/2 flex flex-col">
                    <label for="death_place" class="w-full font-medium pb-1 text-gray-700">{{ __('people.death_place') }}</label>
                    <div class="w-full">
                        <input
                            type="text" class="form-input w-full @error('death_place') invalid @enderror"
                            id="death_place" name="death_place"
                            value="{{ old('death_place', $person->death_place) }}">
                        @error('death_place')
                            <div class="w-full leading-none mt-1">
                                <small class="text-red-500">{{ $message }}</small>
                            </div>
                        @enderror
                    </div>
                </div>
                <x-date-tuple-picker
                    class="w-full sm:w-1/2"
                    name="death_date" :label="__('people.death_date')"
                    :initial-from="old('death_date_from', $person->death_date_from)"
                    :initial-to="old('death_date_to', $person->death_date_to)"/>
            </div>

            <div class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
                <div class="w-full sm:w-1/2 flex flex-col">
                    <label for="funeral_place" class="w-full font-medium pb-1 text-gray-700">{{ __('people.funeral_place') }}</label>
                    <div class="w-full">
                        <input
                            type="text" class="form-input w-full @error('funeral_place') invalid @enderror"
                            id="funeral_place" name="funeral_place"
                            value="{{ old('funeral_place', $person->funeral_place) }}">
                        @error('funeral_place')
                            <div class="w-full leading-none mt-1">
                                <small class="text-red-500">{{ $message }}</small>
                            </div>
                        @enderror
                    </div>
                </div>
                <x-date-tuple-picker
                    class="w-full sm:w-1/2"
                    name="funeral_date" :label="__('people.funeral_date')"
                    :initial-from="old('funeral_date_from', $person->funeral_date_from)"
                    :initial-to="old('funeral_date_to', $person->funeral_date_to)"/>
            </div>

            <div class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
                <div class="w-full sm:w-1/2 flex flex-col">
                    <label for="burial_place" class="w-full font-medium pb-1 text-gray-700">{{ __('people.burial_place') }}</label>
                    <div class="w-full">
                        <input
                            type="text" class="form-input w-full @error('burial_place') invalid @enderror"
                            id="burial_place" name="burial_place"
                            value="{{ old('burial_place', $person->burial_place) }}">
                        @error('burial_place')
                            <div class="w-full leading-none mt-1">
                                <small class="text-red-500">{{ $message }}</small>
                            </div>
                        @enderror
                    </div>
                </div>
                <x-date-tuple-picker
                    class="w-full sm:w-1/2"
                    name="burial_date" :label="__('people.burial_date')"
                    :initial-from="old('burial_date_from', $person->burial_date_from)"
                    :initial-to="old('burial_date_to', $person->burial_date_to)"/>
            </div>
        </fieldset>

        <hr class="mt-7 mb-6">

        <div class="w-full mb-4">
            <div class="font-medium text-xl text-gray-900">{{ __('people.parents') }}</div>
        </div>
        <fieldset class="space-y-5">
            <div class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
                <x-person-picker
                    class="w-full sm:w-1/2"
                    :label="__('people.mother')" sex="xx"
                    name="mother" :nullable="true"
                    :initial="App\Person::find(old('mother_id', $person->mother_id))"/>
                <x-person-picker
                    class="w-full sm:w-1/2"
                    :label="__('people.father')" sex="xy"
                    name="father" :nullable="true"
                    :initial="App\Person::find(old('father_id', $person->father_id))"/>
            </div>
        </fieldset>

        <hr class="mt-7 mb-6">

        <div class="w-full flex items-center justify-between mb-4">
            <div class="font-medium text-xl text-gray-900">{{ __('people.sources') }}</div>
            <button @click.prevent="sources.push('')"
                class="w-6 h-6 p-1 rounded-full border border-blue-700 text-blue-700
                    hover:bg-blue-100 hover:text-blue-800
                    focus:outline-none focus:shadow-outline-none
                    active:bg-blue-600 active:border-blue-600 active:text-blue-100
                    transition-colors duration-100 ease-out">
                <svg class="fill-current" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8 6V2.5H6V6H2.5v2H6v3.5h2V8h3.5V6H8z"/>
                </svg>
            </button>
        </div>
        <fieldset class="space-y-5">
            <div class="w-full">
                <template x-if="sources.length == 0">
                    <input type="hidden" name="sources">
                </template>
                <template x-if="sources.length != 0">
                    <div class="space-y-2">
                        <template
                            x-for="(_, index) in sources" :key="index">
                            <div class="w-full flex flex-no-wrap items-center space-x-2">
                                <input type="text" class="form-input w-full" :name="'sources['+index+']'" x-model="sources[index]">
                                <div>
                                    <button @click.prevent="sources.splice(index, 1)"
                                        class="w-6 h-6 p-1 rounded-full border border-blue-700 text-blue-700
                                            hover:bg-blue-100 hover:text-blue-800
                                            focus:outline-none focus:shadow-outline-none
                                            active:bg-blue-600 active:border-blue-600 active:text-blue-100
                                            transition-colors duration-100 ease-out">
                                        <svg class="fill-current" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M11.5 8h-9V6h9v2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                @error('sources.*')
                    <div class="w-full leading-none mt-1">
                        <small class="text-red-500">{{ $message }}</small>
                    </div>
                @enderror
            </div>
        </fieldset>

        <div class="-m-6 mt-6 px-6 py-4 bg-gray-50 flex justify-end">
            <button type="submit" class="btn">
                {{ __('misc.save') }}
            </button>
        </div>
    </div>
</form>
