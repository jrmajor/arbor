<script lang="ts">
	import type { Snippet } from 'svelte';
	import { writable } from 'svelte/store';
	import { route } from 'ziggy-js';
	import { inertia } from '@inertiajs/svelte';
	import { authLayoutTitle } from '@/helpers/context';

	let { appName, children }: { children: Snippet } & SharedProps = $props();

	const title = authLayoutTitle.set(writable(null));
</script>

<div class="flex justify-center items-center min-h-screen py-12 px-2 sm:px-6 lg:px-8">
	<div class="flex flex-col items-center w-full space-y-4">
		<h1 class="text-3xl sm:text-4xl text-gray-800 leading-none">
			{$title}
		</h1>

		<main class="bg-white rounded-lg shadow px-5 py-4 w-full max-w-[32rem]">
			{@render children()}
		</main>

		<a use:inertia href={route('people.index')} class="a text-base">
			<small>{appName}</small>
		</a>
	</div>
</div>
