@extends('layouts.auth')

@section('title', __('passwords.password_reset'))

@section('content')

	<div class="flex flex-col items-center w-full space-y-4">

		<h1 class="text-3xl sm:text-4xl text-gray-800 leading-none">
			{{ __('passwords.password_reset') }}
		</h1>

		<main class="bg-white rounded-lg shadow px-5 py-4 w-full max-w-[32rem]">
			<form method="POST" action="{{ route('password.email') }}">
				@csrf

				<div class="flex flex-wrap">
					<div class="w-full">
						<input id="email" type="text" class="form-input w-full @error('email') invalid @enderror" name="email" autocomplete="email" placeholder="{{ __('passwords.email') }}">
					</div>
				</div>
				@error('email')
					<div class="w-full leading-none mt-1 text-left">
						<small class="text-red-500">{{ $message }}</small>
					</div>
				@enderror

				<div class="mt-4 flex justify-between items-center">
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
		</main>

		<a href="{{ route('people.index') }}" class="a text-base">
			<small>{{ config('app.name') }}</small>
		</a>

	</div>

@endsection
