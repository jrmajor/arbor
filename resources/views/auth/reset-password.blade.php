@extends('layouts.auth')

@push('scripts')
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
@endpush

@section('title', __('passwords.password_reset'))

@section('content')

    <div class="flex flex-col items-center w-full">

        <a href="{{ route('people.index') }}">
            <h1
                style="font-family: Nunito; letter-spacing: 0.2em"
                class="text-6xl xs:text-7xl sm:text-8xl leading-none">
                arbor
            </h1>
        </a>
        <h2
            style="font-family: Nunito"
            class="text-xl font-medium text-gray-700 leading-none">
            {{ __('passwords.password_reset') }}
        </h2>

        <main class="mt-2 bg-white rounded-lg shadow px-5 py-4 w-full max-w-[32rem]">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="flex flex-wrap">
                    <div class="w-full">
                        <input
                            id="email" type="text" name="email"
                            class="form-input w-full @error('email') invalid @enderror"
                            value="{{ old('email', $email) }}"
                            autocomplete="email"
                            placeholder="{{ __('passwords.email') }}">
                    </div>
                </div>
                @error('email')
                    <div class="w-full leading-none mt-1 text-left">
                        <small class="text-red-500">{{ $message }}</small>
                    </div>
                @enderror

                <div class="flex flex-wrap mt-4">
                    <div class="w-full sm:w-1/2 sm:pr-1 pb-2 sm:pb-0">
                        <input
                            id="password" type="password" name="password"
                            class="form-input w-full @error('password') invalid @enderror"
                            value="{{ old('password') }}"
                            autocomplete="new-password" autofocus
                            placeholder="{{ strtolower(__('passwords.password')) }}">
                    </div>
                    <div class="w-full sm:w-1/2 sm:pl-1">
                        <input
                            id="password-confirm" type="password" name="password_confirmation"
                            class="form-input w-full @error('password') invalid @enderror"
                            autocomplete="current-password"
                            placeholder="{{ strtolower(__('passwords.confirm_password')) }}">
                    </div>
                </div>
                @error('password')
                    <div class="w-full leading-none mt-1 text-left">
                        <small class="text-red-500">{{ $message }}</small>
                    </div>
                @enderror

                <div class="mt-4 flex justify-between items-center">
                    <div>
                        <a href="{{ route('welcome') }}" class="a mr-1"><small>{{ __('misc.cancel') }}</small></a>
                    </div>
                    <div>
                        <button type="submit" class="btn ml-1">
                            {{ __('passwords.reset_password') }}
                        </button>
                    </div>
                </div>
            </form>
        </main>

    </div>

@endsection
