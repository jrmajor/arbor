<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Arbor') }}</title>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <div class="flex justify-center items-center" style="height: 100vh">
            <div class="text-center p-3">
                <h1
                    style="font-family: Nunito; letter-spacing: 0.2em"
                    class="text-7xl md:text-8xl xl:text-10xl">
                    arbor
                </h1>
                <div class="w-full">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="flex flex-wrap mb-4">
                            <div class="w-full sm:w-1/2 sm:pr-1 pb-2 sm:pb-0">
                                <input id="username" type="username" class="@error('username') invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" placeholder="{{ __('auth.username') }}" autofocus>
                            </div>
                            <div class="w-full sm:w-1/2 sm:pl-1">
                                <input id="password" type="password" class="@error('username') invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ __('auth.password') }}">
                            </div>
                        </div>
                        @error('username')
                                <div class="w-full -mt-4 text-left">
                                    <small class="text-red-500">
                                        {{ $message }}
                                    </small>
                                </div>
                        @enderror
                        <div class="flex flex-wrap justify-between items-center mb-5">

                            <div>
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="" for="remember"><small>{{ __('auth.remember') }}</small></label>
                            </div>
                            <div>
                                <a href="{{ route('password.request') }}" class="a mr-1"><small>{{ __('auth.forgot_password') }}</small></a>
                                <button type="submit" class="ml-1">
                                    {{ __('auth.login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>



