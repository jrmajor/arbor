<div
  {{ $attributes->merge(['class' => 'flex flex-col']) }}
  x-data="dateRangePickerData({{ $pickerData() }})"
>
  <div class="w-full pb-1 flex items-center">
    <label for="{{ $name }}_year" class="font-medium text-gray-700">{{ $label }}</label>
    <button
      type="button" x-on:click.prevent="advancedPicker = ! advancedPicker"
      x-text="advancedPicker ? '{{ __('misc.date.simple') }}' : '{{ __('misc.date.advanced') }}'"
      class="ml-2 a underline leading-none text-sm"
    >
      toggle
    </button>
  </div>
  <div class="w-full">
    <div class="flex flex-nowrap items-center justify-between">
      <div x-show="! advancedPicker" class="grow shrink flex">
        <div class="flex items-center">
          <input
            type="text" class="form-input tabular-nums w-32"
            :class="{ 'invalid': ! dateIsValid }"
            x-model="simple" x-on:input="simpleChanged"
            x-on:blur="simpleBlurred"
            placeholder="{{ __('misc.date.format') }}"
            maxlength="10"
          >
        </div>
      </div>
      <div x-show="advancedPicker" class="grow shrink flex flex-wrap -mb-2">
        <div class="grow-0 flex items-center mb-2 mr-1">
          <p class="text-gray-900">{{ __('misc.date.between') }}</p>
          <input
            type="text" class="form-input tabular-nums w-32 ml-1 @error($name.'_from') invalid @enderror"
            x-model="advanced.from" name="{{ $name }}_from"
            placeholder="{{ __('misc.date.format') }}"
            maxlength="10"
          >
        </div>
        <div class="grow-0 flex items-center mb-2">
          <p class="text-gray-900">{{ __('misc.date.and') }}</p>
          <input
            type="text" class="form-input tabular-nums w-32 ml-1 @error($name.'_to') invalid @enderror"
            x-model="advanced.to" name="{{ $name }}_to"
            placeholder="{{ __('misc.date.format') }}"
            maxlength="10"
          >
        </div>
      </div>
    </div>
    @error($name.'_from')
      <div class="w-full leading-none mt-1">
        <small class="text-red-500">{{ $message }}</small>
      </div>
    @enderror
    @error($name.'_to')
      <div class="w-full leading-none mt-1">
        <small class="text-red-500">{{ $message }}</small>
      </div>
    @enderror
  </div>
</div>
