<script lang="ts">
	import type { Snippet } from 'svelte';
	import { formFieldContext } from './contexts';

	let {
		value = $bindable(),
		children,
	}: {
		value: string | null;
		children: Snippet;
	} = $props();

	const formField = formFieldContext.get();
</script>

<select
	id={formField?.id}
	class="form-input w-full"
	class:invalid={formField?.error}
	bind:value
>
	{@render children()}
</select>
