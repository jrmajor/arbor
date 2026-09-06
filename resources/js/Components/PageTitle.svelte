<script lang="ts">
	import type { Snippet } from 'svelte';

	let { children }: { children: Snippet } = $props();
</script>

<h1 class="mt-0.5 -mb-0.5 mx-1.5 leading-none text-3xl font-medium">
	{@render children()}
</h1>
