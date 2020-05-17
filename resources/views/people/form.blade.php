@push('scripts')
    <livewire:styles>
    <livewire:scripts>
@endpush

<form
    method="POST"
    action="{{ $action == 'create' ? route('people.store') : route('people.update', $person) }}"
    x-data="{ sex: '{{ old('sex') ?? $person->sex }}' }">
    @method($action == 'create' ? 'post' : 'put')
    @csrf

    <fieldset class="mb-2">
        <div class="flex flex-wrap mb-1">
            <legend class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('people.sex') }}</legend>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2">
                    <input
                        type="radio" class="mb-1"
                        id="sex_1" name="sex"
                        value="xx" {{ (old('sex') ?? $person->sex) == 'xx' ? 'checked' : '' }}
                        x-model="sex">
                    <label class="" for="sex_1">{{ __('people.female') }}</label>
                    <br>
                    <input
                        type="radio" class="mb-1"
                        id="sex_2" name="sex"
                        value="xy" {{ (old('sex') ?? $person->sex) == 'xy' ? 'checked' : '' }}
                        x-model="sex">
                    <label class="" for="sex_2">{{ __('people.male') }}</label>
                    <br>
                    <input
                        type="radio" class="mb-1"
                        id="sex_3" name="sex"
                        value="" {{ (old('sex') ?? $person->sex) == null ? 'checked' : '' }}
                        x-model="sex">
                    <label class="" for="sex_3">{{ __('people.unknown') }}</label>
                    @error('sex')<br><small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
    </fieldset>
    <fieldset class="mb-2">
        <div class="flex flex-wrap mb-1">
            <label for="name" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('people.name') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="text" class="@error('name') invalid @enderror"
                    id="name" name="name"
                    value="{{ old('name') ?? $person->name }}">
                @error('name')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
        <div class="flex flex-wrap mb-1">
            <label for="middle_name" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('people.middle_name') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="text" class="@error('middle_name') invalid @enderror"
                    id="middle_name" name="middle_name"
                    value="{{ old('middle_name') ?? $person->middle_name }}">
                @error('middle_name')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
        <div class="flex flex-wrap mb-1">
            <label for="family_name" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('people.family_name') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="text" class="@error('family_name') invalid @enderror"
                    id="family_name" name="family_name"
                    value="{{ old('family_name') ?? $person->family_name }}">
                @error('family_name')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
        <div class="flex flex-wrap mb-1">
            <label for="last_name" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('people.last_name') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="text" class="@error('last_name') invalid @enderror"
                    id="last_name" name="last_name"
                    value="{{ old('last_name') ?? $person->last_name }}">
                @error('last_name')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
    </fieldset>
    <fieldset class="mb-2">
        <div class="flex flex-wrap mb-1">
            <label for="id_wielcy" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{!! __('people.wielcy.id') !!}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 flex flex-wrap">
                <div class="w-full sm:w-1/4 md:w-3/8 sm:pr-2 mb-1">
                    <input
                        type="text" class="@error('id_wielcy') invalid @enderror"
                        id="id_wielcy" name="id_wielcy"
                        value="{{ old('id_wielcy') ?? $person->id_wielcy }}">
                    @error('id_wielcy')<small class="text-red-500">{{ $message }}</small>@enderror
                </div>
                <div class="w-full sm:w-3/4 md:w-5/8 mb-1">
                    <input
                        type="text"
                        id="wielcy_search" name="wielcy_search"
                        placeholder="{{ __('misc.coming_soon') }}"
                        disabled>
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset class="mb-2">
        <livewire:pytlewski-picker :person="$person">
    </fieldset>
    {{--
    <div class="flex flex-wrap mb-2">
        <label class="w-full sm:w-1/2 md:w-1/4 pr-1">{{ __('people.pytlewski.guess') }}</label>
        <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 flex flex-wrap">
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
    <fieldset class="mb-2">
        <x-date-tuple-picker
            name="birth_date" :label="__('people.birth_date')"
            :initial-from="old('birth_date_from') ?? $person->birth_date_from"
            :initial-to="old('birth_date_to') ?? $person->birth_date_to"/>
        <div class="flex flex-wrap mb-1">
            <label for="birth_place" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('people.birth_place') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="text" class="@error('birth_place') invalid @enderror"
                    id="birth_place" name="birth_place"
                    value="{{ old('birth_place') ?? $person->birth_place }}">
                @error('birth_place')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
    </fieldset>
    <fieldset class="mb-2">
        <div class="flex flex-wrap mb-1">
            <div class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1"
                x-text="sex == 'xx' ? '{{ __('people.dead_xx') }}' : '{{ __('people.dead_xy') }}'">
                {{ __('people.dead') }}
            </div>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="hidden" id="dead-hidden" name="dead" value="0">
                <input
                    type="checkbox"
                    id="dead" name="dead"
                    value="1" {{ old('dead') ?? $person->dead ? 'checked' : '' }}>
                <label for="dead"
                    x-text="sex == 'xx' ? '{{ __('people.dead_xx') }}' : '{{ __('people.dead_xy') }}'">
                    &nbsp;{{ __('people.dead') }}
                </label>
            </div>
        </div>
        <x-date-tuple-picker
            name="death_date" :label="__('people.death_date')"
            :initial-from="old('death_date_from') ?? $person->death_date_from"
            :initial-to="old('death_date_to') ?? $person->death_date_to"/>
        <div class="flex flex-wrap mb-1">
            <label for="death_place" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('people.death_place') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="text" class="@error('death_place') invalid @enderror"
                    id="death_place" name="death_place"
                    value="{{ old('death_place') ?? $person->death_place }}">
                @error('death_place')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
        <div class="flex flex-wrap mb-1">
            <label for="death_cause" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('people.death_cause') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="text" class="@error('death_cause') invalid @enderror"
                    id="death_cause" name="death_cause"
                    value="{{ old('death_cause') ?? $person->death_cause }}">
                @error('death_cause')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
    </fieldset>
    <fieldset class="mb-2">
        <x-date-tuple-picker
            name="funeral_date" :label="__('people.funeral_date')"
            :initial-from="old('funeral_date_from') ?? $person->funeral_date_from"
            :initial-to="old('funeral_date_to') ?? $person->funeral_date_to"/>
        <div class="flex flex-wrap mb-1">
            <label for="funeral_place" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('people.funeral_place') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="text" class="@error('funeral_place') invalid @enderror"
                    id="funeral_place" name="funeral_place"
                    value="{{ old('funeral_place') ?? $person->funeral_place }}">
                @error('funeral_place')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
    </fieldset>
    <fieldset class="mb-2">
        <x-date-tuple-picker
            name="burial_date" :label="__('people.burial_date')"
            :initial-from="old('burial_date_from') ?? $person->burial_date_from"
            :initial-to="old('burial_date_to') ?? $person->burial_date_to"/>
        <div class="flex flex-wrap mb-1">
            <label for="burial_place" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('people.burial_place') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="text" class="@error('burial_place') invalid @enderror"
                    id="burial_place" name="burial_place"
                    value="{{ old('burial_place') ?? $person->burial_place }}">
                @error('burial_place')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
    </fieldset>
    <fieldset class="mb-2">
        <livewire:person-picker
            :label="__('people.mother')" :sex="'xx'"
            :name="'mother'" :nullable="true"
            :initial="old('mother_id') ?? $person->mother_id">
        <livewire:person-picker
            :label="__('people.father')" :sex="'xy'"
            :name="'father'" :nullable="true"
            :initial="old('father_id') ?? $person->father_id">
    </fieldset>
    <fieldset>
        <div class="flex">
            <div class="w-full sm:w-1/2 md:w-1/4 pr-1"></div>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 flex justify-end">
                <button
                    type="submit" class=" "
                    id="submit" name="submit"
                    value="submit">
                    {{ __('misc.submit') }}
                </button>
            </div>
        </div>
    </fieldset>
</form>
