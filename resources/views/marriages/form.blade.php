<form
    method="POST"
    action="{{ $action == 'create' ? route('marriages.store') : route('marriages.update', ['marriage' => $marriage->id]) }}">
    @method($action == 'create' ? 'post' : 'put')
    @csrf
@foreach($errors as $error)
dd($error)@endforeach
    <pair-picker
        :labels="{{ json_encode(['woman' => __('marriages.woman'), 'man' => __('marriages.man')]) }}"
        :initial-ids="{{ json_encode(['woman' => $marriage->woman_id, 'man' => $marriage->man_id ]) }}">
    </pair-picker>
    <fieldset class="mb-2">
        <div class="flex flex-wrap items-end mb-1">
            <label for="rite" class="w-full sm:w-1/2 md:w-1/4 pr-1 mb-1">{{ __('marriages.rite') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <div class="inline-block relative w-full">
                    <select id="rite" name="rite">
                        <option value="">b/d</option>
                        @foreach(\App\Enums\MarriageRiteEnum::getAll() as $rite)
                            <option
                                value="{{ $rite }}"
                                {{ $rite->isEqual($marriage->rite) ? 'selected' : '' }}>
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
        <div class="flex flex-wrap items-end mb-1">
            <label for="first_event_type" class="w-full sm:w-1/2 md:w-1/4 pr-1 mb-1">{{ __('marriages.first_event_type') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <div class="inline-block relative w-full">
                    <select id="first_event_type" name="first_event_type">
                        <option value="">b/d</option>
                        @foreach(\App\Enums\MarriageEventTypeEnum::getAll() as $type)
                            <option
                                value="{{ $type }}"
                                {{ $type->isEqual($marriage->first_event_type) ? 'selected' : '' }}>
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
        <div class="flex flex-wrap items-end mb-1">
            <label for="first_event_date" class="w-full sm:w-1/2 md:w-1/4 pr-1 mb-1">{{ __('marriages.first_event_date') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="text" class="@error('event_date') invalid @enderror"
                    id="first_event_date" name="first_event_date"
                    value="{{ $marriage->first_event_date }}">
                @error('first_event_date')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
        <div class="flex flex-wrap items-end mb-1">
            <label for="first_event_place" class="w-full sm:w-1/2 md:w-1/4 pr-1 mb-1">{{ __('marriages.first_event_place') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="text" class="@error('event_place') invalid @enderror"
                    id="first_event_place" name="first_event_place"
                    value="{{ $marriage->first_event_place }}">
                @error('first_event_place')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
    </fieldset>
    <fieldset class="mb-2">
        <div class="flex flex-wrap items-end mb-1">
            <label for="second_event_type" class="w-full sm:w-1/2 md:w-1/4 pr-1 mb-1">{{ __('marriages.second_event_type') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <div class="inline-block relative w-full">
                    <select id="second_event_type" name="second_event_type">
                        <option value="">b/d</option>
                        @foreach(\App\Enums\MarriageEventTypeEnum::getAll() as $type)
                            <option
                                value="{{ $type }}"
                                {{ $type->isEqual($marriage->second_event_type) ? 'selected' : '' }}>
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
        <div class="flex flex-wrap items-end mb-1">
            <label for="second_event_date" class="w-full sm:w-1/2 md:w-1/4 pr-1 mb-1">{{ __('marriages.second_event_date') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="text" class="@error('second_event_date') invalid @enderror"
                    id="second_event_date" name="second_event_date"
                    value="{{ $marriage->second_event_date }}">
                @error('second_event_date')<small class="text-red-500">{{ $message }}</small>@enderror
            </div>
        </div>
        <div class="flex flex-wrap items-end mb-1">
            <label for="second_event_place" class="w-full sm:w-1/2 md:w-1/4 pr-1 mb-1">{{ __('marriages.second_event_place') }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                <input
                    type="text" class="@error('second_event_place') invalid @enderror"
                    id="second_event_place" name="second_event_place"
                    value="{{ $marriage->second_event_place }}">
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
