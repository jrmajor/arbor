<script lang="ts">
	import { type Snippet } from 'svelte';
	import { inertia } from '@inertiajs/svelte';
	import { hotkey as hotkeyAction } from '@/helpers/hotkey';
	import { voidAction } from '@/helpers/utils';

	let {
		href,
		hotkey = null,
		external = false,
		children,
	}: {
		href: string | null;
		hotkey?: string | null;
		external?: boolean;
		children: Snippet;
	} = $props();

	let optionalInrtiaAction = $derived(external ? voidAction : inertia);
	let optionalHotkeyAction = $derived(hotkey ? hotkeyAction : voidAction);
</script>

<a
	use:optionalInrtiaAction
	use:optionalHotkeyAction={hotkey}
	{href}
	target={external ? '_blank' : null}
	class="text-blue-700 transition-colors duration-100 hover:text-blue-800 focus:text-blue-800"
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
