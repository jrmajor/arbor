<script lang="ts">
	import type { Snippet } from 'svelte';
	import { randomId } from '@/helpers/utils';

	let {
		checked = $bindable(),
		size = 'md',
		children,
	}: {
		checked: boolean;
		size?: 'sm' | 'md';
		children: Snippet;
	} = $props();

	const id = randomId();
</script>

<div class="flex grow items-center">
	<input
		type="checkbox"
		bind:checked
		{id}
		class="form-checkbox"
		class:size-3.5={size === 'sm'}
	>
	<label for={id} class={size === 'sm' ? 'ml-1.5' : 'ml-2'}>
		{@render children()}
	</label>
</div>
