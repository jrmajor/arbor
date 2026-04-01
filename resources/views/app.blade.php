@extends('base')

@section('head')
	<x-inertia::head>
		<title>{{ config('app.name') }}</title>
	</x-inertia::head>

	@unless (app()->runningUnitTests())
		@vite('resources/css/style.css')
		@vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.svelte"])
	@endif
@endsection

@section('body')
	<x-inertia::app/>
@endsection
