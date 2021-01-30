@extends('layouts.auth')

@push('scripts')
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
@endpush

@section('title', __('auth.signing_in'))

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
            {{ __('auth.signing_in') }}
        </h2>

        <main class="mt-2 bg-white rounded-lg shadow px-5 py-4 w-full xs:w-5/6 sm:w-3/4 md:w-1/2 lg:w-128">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="flex flex-wrap">
                    <div class="w-full sm:w-1/2 sm:pr-1 pb-2 sm:pb-0">
                        <input id="username" type="text"
                        class="form-input w-full @error('username') invalid @enderror @error('password') invalid @enderror"
                        name="username" value="{{ old('username') }}" autocomplete="username" placeholder="{{ strtolower(__('auth.username')) }}" autofocus>
                    </div>
                    <div class="w-full sm:w-1/2 sm:pl-1">
                        <input id="password" type="password"
                        class="form-input w-full @error('username') invalid @enderror @error('password') invalid @enderror"
                        name="password" autocomplete="current-password" placeholder="{{ strtolower(__('auth.password')) }}">
                    </div>
                </div>
                @error('username')
                    <div class="w-full leading-none mt-1 text-left">
                        <small class="text-red-500">{{ $message }}</small>
                    </div>
                @enderror
                @error('password')
                    <div class="w-full leading-none mt-1 text-left">
                        <small class="text-red-500">{{ $message }}</small>
                    </div>
                @enderror

                <div class="mt-4 flex flex-wrap justify-between items-center">
                    <div class="mr-3 flex-grow flex items-center" style="flex-grow: 10">
                        <input type="checkbox" name="remember" id="remember" class="form-checkbox h-3.5 w-3.5" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" class="ml-1"><small>{{ __('auth.remember') }}</small></label>
                    </div>

                    <div class="flex-grow flex items-center justify-between">
                        <a href="{{ route('password.request') }}" class="a mr-1"><small>{{ __('auth.forgot_password') }}</small></a>
                        <button type="submit" class="btn ml-1">
                            {{ __('auth.sign_in') }}
                        </button>
                    </div>
                </div>
            </form>
        </main>

    </div>

@endsection
