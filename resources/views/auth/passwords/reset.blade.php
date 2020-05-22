@extends('layouts.app')

@section('title-bar')
    {{ __('passwords.reset_password') }}
@endsection

@section('content')
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <fieldset class="mb-2">
            <div class="flex flex-wrap items-end mb-1">
                <label for="email" class="w-full sm:w-1/2 md:w-1/4 pr-1 mb-1">{{ __('passwords.email') }}</label>
                <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                    <input
                        type="email" class="@error('email') invalid @enderror"
                        id="email" name="email"
                        value="{{ $email ?? old('email') }}" required
                        autocomplete="email" autofocus>
                    @error('email')<small class="text-red-500">{{ $message }}</small>@enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="mb-2">
            <div class="flex flex-wrap items-end mb-1">
                <label for="password" class="w-full sm:w-1/2 md:w-1/4 pr-1 mb-1">{{ __('passwords.password') }}</label>
                <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                    <input
                        type="password" class="@error('password') invalid @enderror"
                        id="password" name="password"
                        required autocomplete="new-password">
                    @error('password')<small class="text-red-500">{{ $message }}</small>@enderror
                </div>
            </div>
            <div class="flex flex-wrap items-end mb-1">
                <label for="password-confirm" class="w-full sm:w-1/2 md:w-1/4 pr-1 mb-1">{{ __('passwords.confirm_password') }}</label>
                <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 mb-1">
                    <input
                        type="password"
                        id="password-confirm" name="password_confirmation"
                        required autocomplete="new-password">
                    @error('password')<small class="text-red-500">{{ $message }}</small>@enderror
                </div>
            </div>
        </fieldset>

        <fieldset>
            <div class="flex">
                <div class="w-full sm:w-1/2 md:w-1/4 pr-1"></div>
                <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 flex justify-end">
                    <button type="submit" class="btn">
                        {{ __('passwords.reset_password') }}
                    </button>
                </div>
            </div>
        </fieldset>
    </form>
@endsection
