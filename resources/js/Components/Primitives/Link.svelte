<script lang="ts">
	import { type Snippet } from 'svelte';
	import { inertia } from '@inertiajs/svelte';
	import { voidAction } from '@/helpers/utils';

	let {
		href,
		external = false,
		children,
	}: {
		href: string | null;
		external?: boolean;
		children: Snippet;
	} = $props();

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
