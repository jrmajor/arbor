@push('scripts')
    <livewire:styles>
    <livewire:scripts>
@endpush

@section('title', __('settings.settings'))


<div>

    <x-flash/>

    <h1 class="mb-3 mt-4 leading-none text-3xl font-medium">
        {{ __('settings.user').': '.$user->username }}
    </h1>

    <main class="p-6 bg-white rounded-lg shadow">

        <div class="flex flex-col lg:flex-row">
            <label class="w-full lg:w-1/3 lg:w-1/4 font-medium text-xl text-gray-900 mb-4 sm:mb-0">{{ __('settings.email') }}</label>

            <div class="w-full lg:w-2/3 lg:w-3/4">

                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-5 items-end sm:items-center">
                    <input
                        type="text" class="form-input w-full sm:w-auto @error('email') invalid @enderror"
                        wire:model.lazy="email">
                    <button
                        type="button" class="btn flex-grow-0"
                        wire:click="saveEmail">
                        {{ __('misc.save') }}
                    </button>
                </div>

                @error('email')
                    <div class="w-full leading-none mt-1">
                        <small class="text-red-500">{{ $message }}</small>
                    </div>
                @enderror

            </div>
        </div>

        <hr class="mt-7 mb-6">

        <div class="flex flex-col lg:flex-row">
            <label class="w-full lg:w-1/3 lg:w-1/4 font-medium text-xl text-gray-900 mb-4 sm:mb-0">{{ __('settings.change_password') }}</label>

            <div class="w-full lg:w-2/3 lg:w-3/4">

                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-5 items-end sm:items-center">
                    <input
                            type="password" class="form-input w-full sm:w-auto @error('password') invalid @enderror"
                            wire:model.lazy="password"
                            placeholder="{{ strtolower(__('settings.password')) }}">
                    <input
                        type="password" class="form-input w-full sm:w-auto @error('password') invalid @enderror"
                        wire:model.lazy="password_confirmation"
                        placeholder="{{ strtolower(__('settings.confirm_password')) }}">
                    <button
                        type="button" class="btn"
                        wire:click="savePassword">
                        {{ __('misc.save') }}
                    </button>
                </div>

                @error('password')
                    <div class="w-full leading-none mt-1">
                        <small class="text-red-500">{{ $message }}</small>
                    </div>
                @enderror

            </div>
        </div>

        <hr class="mt-7 mb-6">

        <div class="flex flex-col lg:flex-row">
            <label class="w-full lg:w-1/3 lg:w-1/4 font-medium text-xl text-gray-900 mb-4 sm:mb-0">{{ __('settings.logout_other_devices') }}</label>

            <div class="w-full lg:w-2/3 lg:w-3/4">

                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-5 items-end sm:items-center">
                    <input
                        type="password" class="form-input w-full sm:w-auto @error('logout_password') invalid @enderror"
                        wire:model.lazy="logout_password"
                        placeholder="{{ strtolower(__('settings.password')) }}">
                    <button
                        type="button" class="btn"
                        wire:click="logoutOtherDevices">
                        {{ __('settings.logout') }}
                    </button>
                </div>

                @error('logout_password')
                    <div class="w-full leading-none mt-1">
                        <small class="text-red-500">{{ $message }}</small>
                    </div>
                @enderror

            </div>
        </div>

    </main>

</div>
