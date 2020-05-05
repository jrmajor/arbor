@push('scripts')
    <livewire:styles>
    <livewire:scripts>
@endpush

<form
    method="POST"
    action="{{ $action == 'create' ? route('marriages.store') : route('marriages.update', ['marriage' => $marriage->id]) }}">
    @method($action == 'create' ? 'post' : 'put')
    @csrf

    <fieldset class="mb-2">
        <livewire:person-picker
            :label="__('marriages.woman')" :sex="'xx'"
            :name="'woman'" :nullable="false"
            :initial="old('woman_id') ?? $marriage->woman_id">
        <livewire:person-picker
            :label="__('marriages.man')" :sex="'xy'"
            :name="'man'" :nullable="false"
            :initial="old('man_id') ?? $marriage->man_id">
    </fieldset>

    <fieldset class="mb-2">
        <div class="flex flex-wrap mb-1">
            <label for="rite" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('marriages.rite') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
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
                @error('type')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
    </fieldset>

    <fieldset class="mb-2">
        <div class="flex flex-wrap mb-1">
            <label for="first_event_type" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('marriages.first_event_type') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
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
                @error('first_event_type')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
        <div class="flex flex-wrap mb-1">
            <label for="first_event_date_from" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('marriages.first_event_date') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 flex flex-wrap">
                <div class="w-full sm:w-1/2 sm:pr-2 mb-1 flex items-center">
                    <p>{{ __('misc.date.between') }}&nbsp;</p>
                    <input
                        type="text" class="@error('first_event_date_from') invalid @enderror"
                        id="first_event_date_from" name="first_event_date_from"
                        value="{{ old('first_event_date_from') ?? optional($marriage->first_event_date_from)->toDateString() }}">
                    @error('first_event_date_from')<small class="text-red-500">{{ $message }}</small>@enderror
                </div>
                <div class="w-full sm:w-1/2 mb-1 flex items-center">
                    <p>{{ __('misc.date.and') }}&nbsp;</p>
                    <input
                        type="text" class="@error('first_event_date_to') invalid @enderror"
                        id="first_event_date_to" name="first_event_date_to"
                        value="{{ old('first_event_date_to') ?? optional($marriage->first_event_date_to)->toDateString() }}"
                        class="flex-grow">
                    @error('first_event_date_to')<small class="text-red-500">{{ $message }}</small>@enderror
                </div>
            </div>
        </div>
        <div class="flex flex-wrap mb-1">
            <label for="first_event_place" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('marriages.first_event_place') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="text" class="@error('event_place') invalid @enderror"
                    id="first_event_place" name="first_event_place"
                    value="{{ old('first_event_place') ?? $marriage->first_event_place }}">
                @error('first_event_place')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
    </fieldset>

    <fieldset class="mb-2">
        <div class="flex flex-wrap mb-1">
            <label for="second_event_type" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('marriages.second_event_type') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
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
                @error('second_event_type')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
        <div class="flex flex-wrap mb-1">
            <label for="second_event_date_from" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('marriages.second_event_date') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 flex flex-wrap">
                <div class="w-full sm:w-1/2 sm:pr-2 mb-1 flex items-center">
                    <p>{{ __('misc.date.between') }}&nbsp;</p>
                    <input
                        type="text" class="@error('second_event_date_from') invalid @enderror"
                        id="second_event_date_from" name="second_event_date_from"
                        value="{{ old('second_event_date_from') ?? optional($marriage->second_event_date_from)->toDateString() }}">
                    @error('second_event_date_from')<small class="text-red-500">{{ $message }}</small>@enderror
                </div>
                <div class="w-full sm:w-1/2 mb-1 flex items-center">
                    <p>{{ __('misc.date.and') }}&nbsp;</p>
                    <input
                        type="text" class="@error('second_event_date_to') invalid @enderror"
                        id="second_event_date_to" name="second_event_date_to"
                        value="{{ old('second_event_date_to') ?? optional($marriage->second_event_date_to)->toDateString() }}"
                        class="flex-grow">
                    @error('second_event_date_to')<small class="text-red-500">{{ $message }}</small>@enderror
                </div>
            </div>
        </div>
        <div class="flex flex-wrap mb-1">
            <label for="second_event_place" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('marriages.second_event_place') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="text" class="@error('second_event_place') invalid @enderror"
                    id="second_event_place" name="second_event_place"
                    value="{{ old('second_event_place') ?? $marriage->second_event_place }}">
                @error('second_event_place')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
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
