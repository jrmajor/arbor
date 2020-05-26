<div x-data="{ advancedPicker: {{
                $errors->has($name.'_from') || $errors->has($name.'_to') || ! $simplePickerCanBeUsed()
                    ? 'true'
                    : 'false'
            }} }"
    @update-advanced="
        y = $refs.{{ $name }}_year.value;
        m = $refs.{{ $name }}_month.value;
        d = $refs.{{ $name }}_day.value;

        dateIsValid = true;

        if (
            /^([12]\d{3})$|^$/.test(y)
            && /^(0?[1-9]|1[0-2])$|^$/.test(m)
            && /^(0?[1-9]|[12]\d|3[01])$|^$/.test(d)
        ) {
            if (y != '' && m != '' && d != '') {
                f = new Date();
                f.setUTCFullYear(y, parseInt(m)-1, d);
                f = f.toISOString().substring(0,10);
                t = f;
            } else if (y != '' && m != '' && d == '') {
                f = new Date(); t = new Date();
                f.setUTCFullYear(y, parseInt(m)-1, 1);
                t.setUTCFullYear(y, parseInt(m), 0);
                f = f.toISOString().substring(0,10);
                t = t.toISOString().substring(0,10);
            } else if (y != '' && m == '' && d == '') {
                f = new Date(); t = new Date();
                f.setUTCFullYear(y, 0, 1);
                t.setUTCFullYear(y, 12, 0);
                f = f.toISOString().substring(0,10);
                t = t.toISOString().substring(0,10);
            } else if (y == '' && m == '' && d == '') {
                f = '';
                t = '';
            } else {
                dateIsValid = false;
                f = '';
                t = '';
            }

            $refs.{{ $name }}_from.value = f;
            $refs.{{ $name }}_to.value = t;
        } else {
            dateIsValid = false;
            $refs.{{ $name }}_from.value = '';
            $refs.{{ $name }}_to.value = '';
        }

        if (dateIsValid) {
            $refs.{{ $name }}_year.classList.remove('invalid');
            $refs.{{ $name }}_month.classList.remove('invalid');
            $refs.{{ $name }}_day.classList.remove('invalid');
        } else {
            $refs.{{ $name }}_year.classList.add('invalid');
            $refs.{{ $name }}_month.classList.add('invalid');
            $refs.{{ $name }}_day.classList.add('invalid');
        }
    "
    class="flex flex-wrap">
    <label for="{{ $name }}_year" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ $label }}</label>
    <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2">
        <div class="flex flex-nowrap items-center justify-between">
            <div x-show="! advancedPicker"
                class="flex-grow flex-shrink flex">
                <div class="flex items-center space-x-1">
                    <input
                        type="text" class="!w-16"
                        x-ref="{{ $name }}_year"
                        @keyup="$dispatch('update-advanced')"
                        value="{{ $initialSimplePickerValues()['y'] }}"
                        placeholder="{{ __('misc.date.yyyy') }}"
                        maxlength=4>
                    <input
                        type="text" class="!w-12"
                        x-ref="{{ $name }}_month"
                        @keyup="$dispatch('update-advanced')"
                        value="{{ $initialSimplePickerValues()['m'] }}"
                        placeholder="{{ __('misc.date.mm') }}"
                        maxlength=2>
                    <input
                        type="text" class="!w-12"
                        x-ref="{{ $name }}_day"
                        @keyup="$dispatch('update-advanced')"
                        value="{{ $initialSimplePickerValues()['d'] }}"
                        placeholder="{{ __('misc.date.dd') }}"
                        maxlength=2>
                </div>
            </div>
            <div x-show="advancedPicker"
                class="flex-grow flex-shrink flex flex-wrap space-x-1">
                <div class="flex-grow-0 flex items-center space-x-1">
                    <p>{{ __('misc.date.between') }}</p>
                    <input
                        type="text" class="!w-auto @error($name.'_from') invalid @enderror"
                        x-ref="{{ $name }}_from" name="{{ $name }}_from"
                        value="{{ optional($initialFrom)->format('Y-m-d') }}"
                        placeholder="{{ __('misc.date.format') }}"
                        size="12" maxlength=10>
                </div>
                <div class="flex-grow-0 flex items-center space-x-1">
                    <p>{{ __('misc.date.and') }}</p>
                    <input
                        type="text" class="!w-auto @error($name.'_to') invalid @enderror"
                        x-ref="{{ $name }}_to" name="{{ $name }}_to"
                        value="{{ optional($initialTo)->format('Y-m-d') }}"
                        placeholder="{{ __('misc.date.format') }}"
                        size="12" maxlength=10>
                </div>
            </div>
            <div class="flex items-center ml-1">
                <button @click.prevent="advancedPicker = ! advancedPicker"
                    x-text="advancedPicker ? '{{ __('misc.date.simple') }}' : '{{ __('misc.date.advanced') }}'"
                    class=" btn leading-none text-xs !px-2 py-1 normal-case font-normal tracking-normal">
                    toggle
                </button>
                <input type="hidden" name="{{ $name }}[picker]"
                    :value="advancedPicker ? 'advanced' : 'simple'">
            </div>
        </div>
        @error($name.'_from')
            <small class="text-red-500">{{ $message }}</small>
        @enderror
        @error($name.'_to')
            <small class="text-red-500">{{ $message }}</small>
        @enderror
    </div>
</div>
