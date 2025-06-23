<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="font-size: .875em">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		@yield('head')

		<link rel="preconnect" href="https://rsms.me/">
		<link rel="stylesheet" href="https://rsms.me/inter/inter.css">

		@routes

		@if (config('services.fathom.id'))
			<script
				src="https://cdn.usefathom.com/script.js"
				data-site="{{ config('services.fathom.id') }}"
				data-spa="history"
				defer
			></script>
		@endif
	</head>
	<body class="font-sans bg-gray-100">
		@yield('body')
	</body>
</html>
