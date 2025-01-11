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
		class={[
			'rounded border-gray-300 outline-none focus:border-blue-600 focus:ring focus:ring-blue-500/50 focus:ring-offset-0',
			size === 'sm' && 'size-3.5',
		]}
	>
	<label for={id} class={size === 'sm' ? 'ml-1.5' : 'ml-2'}>
		{@render children()}
	</label>
</div>
