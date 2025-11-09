<script lang="ts">
	import type { Snippet } from 'svelte';
	import { getFormFieldContext } from './contexts';

	let { legend = false, children }: {
		legend?: boolean;
		children: Snippet;
	} = $props();

	const formField = getFormFieldContext();
</script>

{#if legend}
	<legend class="font-medium pb-1 text-gray-700">
		{@render children()}
	</legend>
{:else}
	<label for={formField?.id} class="font-medium pb-1 text-gray-700">
		{@render children()}
	</label>
{/if}
