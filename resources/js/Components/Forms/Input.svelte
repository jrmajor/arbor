<script lang="ts">
	import type { HTMLInputAttributes } from 'svelte/elements';
	import { formFieldContext } from './contexts';

	let {
		value = $bindable(),
		type = 'text',
		autofocus = false,
		autocomplete,
		placeholder = '',
		error = null,
	}: {
		value: HTMLInputAttributes['value'];
		type?: 'text' | 'password';
		autofocus?: HTMLInputAttributes['autofocus'];
		autocomplete?: HTMLInputAttributes['autocomplete'];
		placeholder?: HTMLInputAttributes['placeholder'];
		error?: string | null | undefined;
	} = $props();

	const formField = formFieldContext.get();
</script>

<!-- svelte-ignore a11y_autofocus -->
<input
	id={formField?.id}
	{type}
	bind:value
	{autofocus}
	{autocomplete}
	{placeholder}
	class="form-input w-full"
	class:invalid={formField?.error || error}
/>
