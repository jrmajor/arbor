<script lang="ts">
	import type { Snippet } from 'svelte';

	let {
		checked = $bindable(),
		size = 'md',
		children,
	}: {
		checked: boolean;
		size?: 'sm' | 'md';
		children: Snippet;
	} = $props();

	const id = $props.id();
</script>

<div class="flex grow items-center">
	<input
		type="checkbox"
		bind:checked
		{id}
		class={[
			'rounded-sm border-gray-300 outline-none checked:border-transparent',
			'focus:border-blue-600 focus:ring-3 focus:ring-blue-500/50 focus:ring-offset-0',
			size === 'sm' && 'size-3.5',
		]}
	>
	<label for={id} class={size === 'sm' ? 'ml-1.5' : 'ml-2'}>
		{@render children()}
	</label>
</div>
