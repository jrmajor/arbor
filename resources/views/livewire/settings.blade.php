@push('scripts')
    <livewire:styles>
    <livewire:scripts>
@endpush

@section('title', __('settings.settings'))

@section('title-bar', __('settings.user').': '.$user->username)

<div>

    <form wire:submit.prevent="submit"
        class="space-y-4">

        <fieldset class="space-y-2">
            <div class="flex flex-wrap">
                <label for="email" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('settings.email') }}</label>
                <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2">
                    <input
                        type="text" class="@error('email') invalid @enderror"
                        wire:model.lazy="email"
                    @error('email')
                        <small class="text-red-500">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="space-y-2">
            <div class="flex flex-wrap">
                <label for="password" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('settings.password') }}</label>
                <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2">
                    <input
                        type="password" class="@error('password') invalid @enderror"
                        wire:model.lazy="password">
                </div>
            </div>
            <div class="flex flex-wrap">
                <label for="password_confirmation" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ __('settings.confirm_password') }}</label>
                <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2">
                    <input
                        type="password" class="@error('password') invalid @enderror"
                        wire:model.lazy="password_confirmation">
                    @error('password')
                        <small class="text-red-500">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="w-full lg:w-3/4 flex justify-end">
            <button
                type="submit" class="btn">
                {{ __('misc.submit') }}
            </button>
        </fieldset>
    </form>

</div>
