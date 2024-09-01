<script lang="ts">
	import { type Snippet } from 'svelte';
	import type { Action } from 'svelte/action';
	import { inertia } from '@inertiajs/svelte';

	let {
		href,
		external = false,
		children,
	}: {
		href: string | null;
		external?: boolean;
		children: Snippet;
	} = $props();

	// eslint-disable-next-line func-style
	const voidAction: Action<HTMLButtonElement, any> = () => ({});

	let action = $derived(external ? voidAction : inertia);
</script>

<a
	use:action
	{href}
	target={external ? '_blank' : null}
	class="text-blue-700 transition-colors duration-100 hover:text-blue-800 focus:text-blue-800"
>
	{@render children()}
</a>

<style lang="postcss">
	a :global(small) {
		@apply text-gray-700;
	}

	a:hover :global(small), a:focus :global(small) {
		@apply text-gray-900;
	}
</style>
