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
                class="text-7xl xs:text-8xl sm:text-9xl md:text-9xl xl:text-9xl leading-none">
                arbor
            </h1>
        </a>
        <h2
            style="font-family: Nunito"
            class="text-xl font-medium text-gray-700 leading-none">
            {{ __('auth.signing_in') }}
        </h2>
        <div class="mt-2 bg-white rounded-lg shadow-lg px-5 py-4 w-full xs:w-5/6 sm:w-3/4 md:w-1/2 lg:w-128">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="flex flex-wrap mb-4">
                    <div class="w-full sm:w-1/2 sm:pr-1 pb-2 sm:pb-0">
                        <input id="username" type="username" class="@error('username') invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" placeholder="{{ strtolower(__('auth.username')) }}" autofocus>
                    </div>
                    <div class="w-full sm:w-1/2 sm:pl-1">
                        <input id="password" type="password" class="@error('username') invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ strtolower(__('auth.password')) }}">
                    </div>
                </div>
                @error('username')
                    <div class="w-full -mt-4 text-left">
                        <small class="text-red-500">
                            {{ $message }}
                        </small>
                    </div>
                @enderror
                <div class="flex flex-wrap justify-between items-center">
                    <div>
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="" for="remember"><small>{{ __('auth.remember') }}</small></label>
                    </div>

                    <div>
                        <a href="{{ route('password.request') }}" class="a mr-1"><small>{{ __('auth.forgot_password') }}</small></a>
                        <button type="submit" class="btn ml-1">
                            {{ __('auth.sign_in') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
