<script lang="ts">
	import type { Snippet } from 'svelte';
	import { languageContext } from '@/helpers/translations';

	let { children }: { children: Snippet } = $props();

	languageContext.set(window.userLanguage);
</script>

{@render children()}
