<script lang="ts">
	import type { Snippet } from 'svelte';
	import { formFieldContext } from './contexts';

	let { legend = false, children }: {
		legend?: boolean;
		children: Snippet;
	} = $props();

	const formField = formFieldContext.get();
</script>

{#if legend}
	<legend class="font-medium pb-1 text-gray-700">
		{@render children()}
	</legend>
{:else}
	<label for={formField.id} class="font-medium pb-1 text-gray-700">
		{@render children()}
	</label>
{/if}
