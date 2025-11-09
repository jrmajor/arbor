<script lang="ts">
	import type { Snippet } from 'svelte';
	import { getFormFieldContext, getRadioGroupContext } from './contexts';

	let { value, children }: {
		value: string | null;
		children: Snippet;
	} = $props();

	const id = $props.id();

	const formField = getFormFieldContext();
	const radioContext = getRadioGroupContext();

	const checked = $derived(radioContext.value === value);

	function onchange(event: Event) {
		const target = event.target as HTMLInputElement;
		if (!target.checked) return;
		radioContext.value = value;
	}
</script>

<div class="w-full sm:w-auto flex items-center">
	<input
		type="radio"
		name={radioContext.id}
		{id}
		{checked}
		{value}
		class={[
			'border-gray-300 outline-none checked:border-transparent',
			'focus:border-blue-600 focus:ring-3 focus:ring-blue-500/50 focus:ring-offset-0',
			formField?.error && 'border-red-600 text-red-500 focus:ring-red-500/50',
		]}
		{onchange}
	>
	<label class="ml-2" for={id}>
		{@render children()}
	</label>
</div>
