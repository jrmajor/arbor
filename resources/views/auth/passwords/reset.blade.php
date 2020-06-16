@extends('layouts.auth')

@push('scripts')
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
@endpush

@section('title', __('passwords.resetting_password'))

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
            {{ __('passwords.resetting_password') }}
        </h2>

        <main class="mt-2 bg-white rounded-lg shadow-lg px-5 py-4 w-full xs:w-5/6 sm:w-3/4 md:w-1/2 lg:w-128">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="flex flex-wrap mb-4">
                    <div class="w-full">
                        <input
                            id="email" type="text" name="email"
                            class="@error('email') invalid @enderror"
                            value="{{ $email ?? old('email') }}"
                            autocomplete="email"
                            placeholder="{{ __('passwords.email') }}">
                    </div>
                </div>
                @error('email')
                    <div class="w-full -mt-4 text-left">
                        <small class="text-red-500">
                            {{ $message }}
                        </small>
                    </div>
                @enderror

                <div class="flex flex-wrap mb-4">
                    <div class="w-full sm:w-1/2 sm:pr-1 pb-2 sm:pb-0">
                        <input
                            id="password" type="password" name="password"
                            class="@error('password') invalid @enderror"
                            value="{{ old('password') }}"
                            autocomplete="new-password" autofocus
                            placeholder="{{ strtolower(__('passwords.password')) }}">
                    </div>
                    <div class="w-full sm:w-1/2 sm:pl-1">
                        <input
                            id="password-confirm" type="password" name="password_confirmation"
                            class="@error('password') invalid @enderror"
                            autocomplete="current-password"
                            placeholder="{{ strtolower(__('passwords.confirm_password')) }}">
                    </div>
                </div>
                @error('password')
                    <div class="w-full -mt-4 text-left">
                        <small class="text-red-500">
                            {{ $message }}
                        </small>
                    </div>
                @enderror

                <div class="flex justify-end">
                    <div>
                        <button type="submit" class="btn">
                            {{ __('passwords.reset_password') }}
                        </button>
                    </div>
                </div>
            </form>
        </main>

    </div>

@endsection
