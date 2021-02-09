@push('scripts')
    <livewire:styles>
    <livewire:scripts>
@endpush

<form
    method="POST"
    action="{{ $action === 'create' ? route('marriages.store') : route('marriages.update', $marriage) }}"
    x-data="
        @encodedjson([
            'divorced' => (bool) old('divorced', $marriage->divorced),
        ])
    ">
    @method($action === 'create' ? 'post' : 'put')
    @csrf

    <div>
        <fieldset class="space-y-5">
            <div class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
                <x-person-picker
                    class="w-full sm:w-1/2"
                    :label="__('marriages.woman')" sex="xx"
                    name="woman" :nullable="false"
                    :initial="old('woman_id', $marriage->woman_id)"/>
                <x-person-picker
                    class="w-full sm:w-1/2"
                    :label="__('marriages.man')" sex="xy"
                    name="man" :nullable="false"
                    :initial="old('man_id', $marriage->man_id)"/>
            </div>

            <div class="w-full space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
                <div class="flex flex-row space-x-5">
                    <div class="flex flex-col">
                        <label for="woman_order" class="w-full font-medium pb-1 text-gray-700">{{ __('marriages.woman_order') }}</label>
                        <div class="w-full">
                            <input
                                type="text" class="form-input w-full @error('woman_order') invalid @enderror"
                                id="woman_order" name="woman_order"
                                value="{{ old('woman_order', $marriage->woman_order) }}">
                            @error('woman_order')
                                <div class="w-full leading-none mt-1">
                                    <small class="text-red-500">{{ $message }}</small>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="man_order" class="w-full font-medium pb-1 text-gray-700">{{ __('marriages.man_order') }}</label>
                        <div class="w-full">
                            <input
                                type="text" class="form-input w-full @error('man_order') invalid @enderror"
                                id="man_order" name="man_order"
                                value="{{ old('man_order', $marriage->man_order) }}">
                            @error('man_order')
                                <div class="w-full leading-none mt-1">
                                    <small class="text-red-500">{{ $message }}</small>
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="flex-grow flex flex-col">
                    <label for="rite" class="w-full font-medium pb-1 text-gray-700">{{ __('marriages.rite') }}</label>
                    <div class="w-full">
                        <select id="rite" name="rite" class="form-select w-full">
                            <option value="">b/d</option>
                            @foreach(\App\Enums\MarriageRiteEnum::toArray() as $rite)
                                <option
                                    value="{{ $rite }}"
                                    {{ $rite === old('rite', (string) $marriage->rite) ? 'selected' : '' }}>
                                    {{ __('marriages.rites.' . $rite) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </fieldset>

        <hr class="mt-7 mb-6">

        <div class="w-full mb-4">
            <div class="font-medium text-xl text-gray-900">{{ __('marriages.first_event') }}</div>
        </div>
        <fieldset>
            <!-- https://bugs.chromium.org/p/chromium/issues/detail?id=375693 -->
            <div class="space-y-5 lg:space-y-0 lg:space-x-5 flex flex-col lg:flex-row">
                <div class="w-full lg:w-1/3 flex flex-col">
                    <label for="first_event_type" class="w-full font-medium pb-1 text-gray-700">{{ __('marriages.event_type') }}</label>
                    <div class="w-full">
                        <select id="first_event_type" name="first_event_type" class="form-select w-full">
                            <option value="">b/d</option>
                            @foreach(\App\Enums\MarriageEventTypeEnum::toArray() as $type)
                                <option
                                    value="{{ $type }}"
                                    {{ $type === old('first_event_type', (string) $marriage->first_event_type) ? 'selected' : '' }}>
                                    {{ __('marriages.event_types.' . $type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-5 sm:space-y-0 sm:space-x-5 w-full sm:w-full lg:w-2/3 flex flex-col sm:flex-row">
                    <div class="w-full sm:w-1/2 flex flex-col">
                        <label for="first_event_place" class="w-full font-medium pb-1 text-gray-700">{{ __('misc.place') }}</label>
                        <div class="w-full">
                            <input
                                type="text" class="form-input w-full @error('first_event_place') invalid @enderror"
                                id="first_event_place" name="first_event_place"
                                value="{{ old('first_event_place', $marriage->first_event_place) }}">
                            @error('first_event_place')
                                <div class="w-full leading-none mt-1">
                                    <small class="text-red-500">{{ $message }}</small>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <x-date-tuple-picker
                        class="w-full sm:w-1/2"
                        name="first_event_date" :label="__('misc.date.date')"
                        :initial-from="old('first_event_date_from', $marriage->first_event_date_from)"
                        :initial-to="old('first_event_date_to', $marriage->first_event_date_to)"/>
                </div>
            </div>
        </fieldset>

        <hr class="mt-7 mb-6">

        <div class="w-full mb-4">
            <div class="font-medium text-xl text-gray-900">{{ __('marriages.second_event') }}</div>
        </div>
        <fieldset>
            <!-- https://bugs.chromium.org/p/chromium/issues/detail?id=375693 -->
            <div class="space-y-5 lg:space-y-0 lg:space-x-5 flex flex-col lg:flex-row">
                <div class="w-full lg:w-1/3 flex flex-col">
                    <label for="second_event_type" class="w-full font-medium pb-1 text-gray-700">{{ __('marriages.event_type') }}</label>
                    <div class="w-full">
                        <select id="second_event_type" name="second_event_type" class="form-select w-full">
                            <option value="">b/d</option>
                            @foreach(\App\Enums\MarriageEventTypeEnum::toArray() as $type)
                                <option
                                    value="{{ $type }}"
                                    {{ $type === old('second_event_type', (string) $marriage->second_event_type) ? 'selected' : '' }}>
                                    {{ __('marriages.event_types.' . $type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-5 sm:space-y-0 sm:space-x-5 w-full sm:w-full lg:w-2/3 flex flex-col sm:flex-row">
                    <div class="w-full sm:w-1/2 flex flex-col">
                        <label for="second_event_place" class="w-full font-medium pb-1 text-gray-700">{{ __('misc.place') }}</label>
                        <div class="w-full">
                            <input
                                type="text" class="form-input w-full @error('second_event_place') invalid @enderror"
                                id="second_event_place" name="second_event_place"
                                value="{{ old('second_event_place', $marriage->second_event_place) }}">
                            @error('second_event_place')
                                <div class="w-full leading-none mt-1">
                                    <small class="text-red-500">{{ $message }}</small>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <x-date-tuple-picker
                        class="w-full sm:w-1/2"
                        name="second_event_date" :label="__('misc.date.date')"
                        :initial-from="old('second_event_date_from', $marriage->second_event_date_from)"
                        :initial-to="old('second_event_date_to', $marriage->second_event_date_to)"/>
                </div>
            </div>
        </fieldset>

        <hr class="mt-7 mb-6">

        <div class="w-full flex items-center mb-4">
            <label for="divorced" class="font-medium text-xl text-gray-900">{{ __('marriages.divorce') }}</label>
            <input type="hidden" id="divorced-hidden" name="divorced" value="0">
            <input
                type="checkbox" class="form-checkbox ml-2 h-4 w-4"
                id="divorced" name="divorced"
                value="1"
                x-model="divorced">
        </div>
        <fieldset>
            <!-- https://bugs.chromium.org/p/chromium/issues/detail?id=375693 -->
            <div class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row" x-show="divorced">
                <div class="w-full sm:w-1/2 flex flex-col">
                    <label for="divorce_place" class="w-full font-medium pb-1 text-gray-700">{{ __('misc.place') }}</label>
                    <div class="w-full">
                        <input
                            type="text" class="form-input w-full @error('divorce_place') invalid @enderror"
                            id="divorce_place" name="divorce_place"
                            value="{{ old('divorce_place', $marriage->divorce_place) }}">
                        @error('divorce_place')
                            <div class="w-full leading-none mt-1">
                                <small class="text-red-500">{{ $message }}</small>
                            </div>
                        @enderror
                    </div>
                </div>
                <x-date-tuple-picker
                    class="w-full sm:w-1/2"
                    name="divorce_date" :label="__('misc.date.date')"
                    :initial-from="old('divorce_date_from', $marriage->divorce_date_from)"
                    :initial-to="old('divorce_date_to', $marriage->divorce_date_to)"/>
            </div>
        </fieldset>

        <div class="-m-6 mt-6 px-6 py-4 bg-gray-50 flex justify-end">
            <button type="submit" class="btn">
                {{ __('misc.save') }}
            </button>
        </div>
    </div>
</form>
