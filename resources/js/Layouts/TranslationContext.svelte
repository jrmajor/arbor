<script lang="ts">
	import type { Snippet } from 'svelte';

	let {
		currentLocale,
		fallbackLocale,
		children,
	}: { children: Snippet } & SharedProps = $props();

	globalThis.globalProps = { currentLocale, fallbackLocale };
</script>

{@render children()}
