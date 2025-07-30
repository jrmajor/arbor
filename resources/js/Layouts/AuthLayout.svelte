<script lang="ts">
	import type { Snippet } from 'svelte';
	import { writable } from 'svelte/store';
	import { route } from 'ziggy-js';
	import { authLayoutTitle } from '@/helpers/context';
	import Link from '@/Components/Primitives/Link.svelte';

	let { appName, children }: { children: Snippet } & SharedProps = $props();

	const title = authLayoutTitle.set(writable(null));
</script>

<div class="auth-container flex justify-center items-center min-h-dvh py-12 px-2 sm:px-6 lg:px-8">
	<div class="flex flex-col items-center w-full space-y-4">
		<h1 class="text-3xl sm:text-4xl text-gray-800 leading-none">
			{$title}
		</h1>

		<main class="bg-white rounded-lg shadow-sm px-5 py-4 w-full max-w-[32rem]">
			{@render children()}
		</main>

		<Link href={route('people.index')}>
			<small>{appName}</small>
		</Link>
	</div>
</div>

<style>
	.auth-container {
		margin:
			env(safe-area-inset-top)
			env(safe-area-inset-right)
			env(safe-area-inset-bottom)
			env(safe-area-inset-left);
	}
	</style>
