<script lang="ts">
	import type { Snippet } from 'svelte';
	import { setLocale } from '@/helpers/translationsState.svelte';

	let {
		currentLocale,
		fallbackLocale,
		children,
	}: { children: Snippet } & SharedProps = $props();

	// svelte-ignore state_referenced_locally
	setLocale(currentLocale, fallbackLocale);
	$effect(() => setLocale(currentLocale, fallbackLocale));
</script>

{@render children()}
