<script lang="ts">
	import type { Snippet } from 'svelte';
	import Flash from './Flash.svelte';

	let { flash, children }: {
		flash: FlashData | null;
		children: Snippet;
	} = $props();

	const currentYear = new Date().getFullYear();
</script>

<div class="container mx-auto my-1 p-2 pt-5">
	{#if flash}
		<Flash {...flash}/>
	{/if}

	{@render children()}

	<footer class="m-1 px-3 pt-2 text-center text-gray-600 text-sm">
		&copy; 2018-{currentYear} <a href={`mailto:${atob('anJoLm1qckBnbWFpbC5jb20=')}`}>Jeremiasz Major</a>
	</footer>
</div>
