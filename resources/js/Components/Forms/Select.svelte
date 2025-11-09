<script lang="ts">
	import type { Snippet } from 'svelte';
	import { getFormFieldContext } from './contexts';

	let {
		value = $bindable(),
		children,
	}: {
		value: string | null;
		children: Snippet;
	} = $props();

	const formField = getFormFieldContext();
</script>

<select
	id={formField?.id}
	class="form-input w-full"
	class:invalid={formField?.error}
	bind:value
>
	{@render children()}
</select>
