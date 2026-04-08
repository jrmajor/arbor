<script lang="ts">
	import { type Snippet } from 'svelte';
	import type { HTMLAnchorAttributes } from 'svelte/elements';
	import { inertia } from '@inertiajs/svelte';
	import { voidAction } from '@/helpers/utils';

	let {
		href,
		external = false,
		children,
		...props
	}: {
		href: string | null;
		external?: boolean;
		children: Snippet;
	} & Pick<HTMLAnchorAttributes, symbol> = $props();

	let optionalInrtiaAction = $derived(external ? voidAction : inertia);
</script>

<a
	use:optionalInrtiaAction
	{href}
	target={external ? '_blank' : null}
	class="text-blue-700 transition-colors duration-100 hover:text-blue-800 focus:text-blue-800"
	{...props}
>
	{@render children()}
</a>

<style>
	@reference '$style';

	a :global(small) {
		@apply text-gray-700;
	}

	a:hover :global(small), a:focus :global(small) {
		@apply text-gray-900;
	}
</style>
