<script lang="ts">
	import type { Snippet } from 'svelte';

	let { children }: { children: Snippet } = $props();
</script>

<h1 class="leading-none text-3xl font-medium">
	{@render children()}
</h1>
