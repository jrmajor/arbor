<script lang="ts">
	import type { Snippet } from 'svelte';
	import { route } from 'ziggy-js';

	let { title, children }: {
		title: string;
		children: Snippet;
	} = $props();
</script>

<div class="flex justify-center items-center min-h-screen py-12 px-2 sm:px-6 lg:px-8">
	<div class="flex flex-col items-center w-full space-y-4">
		<h1 class="text-3xl sm:text-4xl text-gray-800 leading-none">
			{title}
		</h1>

		<main class="bg-white rounded-lg shadow px-5 py-4 w-full max-w-[32rem]">
			{@render children()}
		</main>

		<a href={route('people.index')} class="a text-base">
			<small>{globalThis.arborProps.appName}</small>
		</a>
	</div>
</div>
