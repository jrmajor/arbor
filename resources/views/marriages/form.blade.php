@push('scripts')
    <livewire:styles>
    <livewire:scripts>
@endpush

<form
    method="POST"
    action="{{ $action == 'create' ? route('marriages.store') : route('marriages.update', $marriage) }}">
    @method($action == 'create' ? 'post' : 'put')
    @csrf

    <div class="space-y-4">
        <fieldset class="space-y-2">
            <livewire:person-picker
                :label="__('marriages.woman')" :sex="'xx'"
                :name="'woman'" :nullable="false"
                :initial="old('woman_id') ?? $marriage->woman_id">
            <livewire:person-picker
                :label="__('marriages.man')" :sex="'xy'"
                :name="'man'" :nullable="false"
                :initial="old('man_id') ?? $marriage->man_id">
        </fieldset>

        <fieldset class="space-y-2">
            <div class="flex flex-wrap">
                <label for="rite" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('marriages.rite') }}</label>
                <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2">
                    <div class="inline-block relative w-full">
                        <select id="rite" name="rite">
                            <option value="">b/d</option>
                            @foreach(\App\Enums\MarriageRiteEnum::getAll() as $rite)
                                <option
                                    value="{{ $rite }}"
                                    {{ $rite->isEqual(old('rite') ?? $marriage->rite) ? 'selected' : '' }}>
                                    {{ __('marriages.rites.' . $rite) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset class="space-y-2">
            <div class="flex flex-wrap">
                <label for="first_event_type" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('marriages.first_event_type') }}</label>
                <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2">
                    <div class="inline-block relative w-full">
                        <select id="first_event_type" name="first_event_type">
                            <option value="">b/d</option>
                            @foreach(\App\Enums\MarriageEventTypeEnum::getAll() as $type)
                                <option
                                    value="{{ $type }}"
                                    {{ $type->isEqual(old('first_event_type') ?? $marriage->first_event_type) ? 'selected' : '' }}>
                                    {{ __('marriages.event_types.' . $type) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <x-date-tuple-picker
                name="first_event_date" :label="__('marriages.first_event_date')"
                :initial-from="old('first_event_date_from') ?? $marriage->first_event_date_from"
                :initial-to="old('first_event_date_to') ?? $marriage->first_event_date_to"/>
            <div class="flex flex-wrap">
                <label for="first_event_place" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('marriages.first_event_place') }}</label>
                <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2">
                    <input
                        type="text" class="@error('event_place') invalid @enderror"
                        id="first_event_place" name="first_event_place"
                        value="{{ old('first_event_place') ?? $marriage->first_event_place }}">
                    @error('first_event_place')
                        <small class="text-red-500">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="space-y-2">
            <div class="flex flex-wrap">
                <label for="second_event_type" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('marriages.second_event_type') }}</label>
                <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2">
                    <div class="inline-block relative w-full">
                        <select id="second_event_type" name="second_event_type">
                            <option value="">b/d</option>
                            @foreach(\App\Enums\MarriageEventTypeEnum::getAll() as $type)
                                <option
                                    value="{{ $type }}"
                                    {{ $type->isEqual(old('second_event_type') ?? $marriage->second_event_type) ? 'selected' : '' }}>
                                    {{ __('marriages.event_types.' . $type) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <x-date-tuple-picker
                name="second_event_date" :label="__('marriages.second_event_date')"
                :initial-from="old('second_event_date_from') ?? $marriage->second_event_date_from"
                :initial-to="old('second_event_date_to') ?? $marriage->second_event_date_to"/>
            <div class="flex flex-wrap">
                <label for="second_event_place" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('marriages.second_event_place') }}</label>
                <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2">
                    <input
                        type="text" class="@error('second_event_place') invalid @enderror"
                        id="second_event_place" name="second_event_place"
                        value="{{ old('second_event_place') ?? $marriage->second_event_place }}">
                    @error('second_event_place')
                        <small class="text-red-500">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="w-full lg:w-3/4 flex justify-end">
            <button type="submit" class="btn">
                {{ __('misc.save') }}
            </button>
        </fieldset>
    </div>
</form>
