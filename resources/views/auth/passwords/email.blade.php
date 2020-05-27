{{--

            <div class="space-y-4">
                <fieldset class="space-y-2">
                    <div class="flex flex-wrap">
                        <label for="email" class="w-full sm:w-1/2 md:w-1/4 pr-1">{{ __('passwords.email') }}</label>
                        <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2">
                            <input
                                type="email" class="@error('email') invalid @enderror"
                                id="email" name="email"
                                value="{{ old('email') }}" required
                                autocomplete="email" autofocus>
                            @error('email')<small class="text-red-500">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </fieldset>

                <fieldset class="w-full lg:w-3/4 flex justify-end">
                    <button
                        type="submit" class="btn"
                        id="submit" name="submit"
                        value="submit">
                        {{ __('passwords.send_link') }}
                    </button>
                </fieldset>
            </div>
 --}}

@extends('layouts.auth')

@push('scripts')
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
@endpush

@section('title', __('passwords.resetting_password'))

@section('content')

    @if (session('status'))

        <strong class="text-green-500">
            {{ session('status') }}
        </strong>

    @else

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
            <div class="mt-2 bg-white rounded-lg shadow-lg px-5 py-4 w-full xs:w-5/6 sm:w-3/4 md:w-1/2 lg:w-128">
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="flex flex-wrap mb-4">
                        <div class="w-full">
                            <input id="email" type="text" class="@error('email') invalid @enderror" name="email" autocomplete="email" placeholder="{{ __('passwords.email') }}">
                        </div>
                    </div>
                    @error('email')
                        <div class="w-full -mt-4 text-left">
                            <small class="text-red-500">
                                {{ $message }}
                            </small>
                        </div>
                    @enderror
                    <div class="flex justify-between items-center">
                        <div>
                            <a href="{{ route('login') }}" class="a mr-1"><small>{{ __('auth.sign_in') }}</small></a>
                        </div>
                        <div>
                            <button type="submit" class="btn ml-1">
                                {{ __('passwords.send_link') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    @endif

@endsection
