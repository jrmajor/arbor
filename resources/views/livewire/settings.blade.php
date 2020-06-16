@push('scripts')
    <livewire:styles>
    <livewire:scripts>
@endpush

@section('title', __('settings.settings'))


<div>

    <x-flash/>

    <h1 class="mx-2 mb-1 mt-5 leading-none text-3xl font-medium">
        {{ __('settings.user').': '.$user->username }}
    </h1>

    <main class="p-4 bg-white rounded-lg shadow-lg space-y-4">

        <div>
            <h2 class="mb-1 leading-none text-2xl font-medium">{{ __('settings.email') }}</h2>

            <div class="flex flex-wrap sm:flex-no-wrap space-y-2 sm:space-y-0 sm:space-x-2 items-center">
                <div class="w-full flex sm:w-3/4 lg:w-1/2 items-center">
                    <label for="email" class="mr-2">{{ __('settings.email') }}</label>
                    <div class="flex-grow">
                        <input
                            type="text" class="@error('email') invalid @enderror"
                            wire:model.lazy="email">
                    </div>
                </div>
                <div class="w-full flex sm:w-1/4 justify-end">
                    <button
                        type="button" class="btn"
                        wire:click="saveEmail">
                        {{ __('misc.save') }}
                    </button>
                </div>
            </div>
            @error('email')
                <small class="text-red-500">{{ $message }}</small>
            @enderror
        </div>

        <div>
            <h2 class="mb-1 leading-none text-2xl font-medium">{{ __('settings.change_password') }}</h2>

            <div class="flex flex-wrap sm:flex-no-wrap space-y-2 sm:space-y-0 sm:space-x-2 items-center">
                <div class="flex w-full flex-wrap sm:flex-no-wrap items-center sm:space-x-2 sm:w-3/4 lg:w-1/2">
                    <label for="password" class="w-full sm:w-auto">{{ __('settings.password') }}</label>
                    <div class="w-full sm:w-auto flex-grow">
                        <input
                            type="password" class="@error('password') invalid @enderror"
                            wire:model.lazy="password"
                            placeholder="{{ strtolower(__('settings.password')) }}">
                    </div>
                    <div class="w-full mt-2 sm:mt-0 sm:w-auto flex-grow">
                        <input
                            type="password" class="@error('password') invalid @enderror"
                            wire:model.lazy="password_confirmation"
                            placeholder="{{ strtolower(__('settings.confirm_password')) }}">
                    </div>
                </div>

                <div class="w-full flex justify-end sm:w-1/4">
                    <button
                        type="button" class="btn"
                        wire:click="savePassword">
                        {{ __('misc.save') }}
                    </button>
                </div>
            </div>
            @error('password')
                <small class="text-red-500">{{ $message }}</small>
            @enderror
        </div>

        <div>
            <h2 class="mb-1 leading-none text-2xl font-medium">{{ __('settings.logout_other_devices') }}</h2>

            <div class="flex flex-wrap sm:flex-no-wrap space-y-2 sm:space-y-0 sm:space-x-2 items-center">
                <div class="w-full flex sm:w-3/4 lg:w-1/2 items-center">
                    <label for="password" class="mr-2">{{ __('settings.password') }}</label>
                    <div class="flex-grow">
                        <input
                            type="password" class="@error('logout_password') invalid @enderror"
                            wire:model.lazy="logout_password">
                    </div>
                </div>
                <div class="w-full flex sm:w-1/4 justify-end">
                    <button
                        type="button" class="btn"
                        wire:click="logoutOtherDevices">
                        {{ __('settings.logout') }}
                    </button>
                </div>
            </div>
            @error('logout_password')
                <small class="text-red-500">{{ $message }}</small>
            @enderror
        </div>

    </main>

</div>
