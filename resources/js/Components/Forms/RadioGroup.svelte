<script lang="ts">
	import type { Snippet } from 'svelte';
	import { radioGroupContext } from './contexts';

	let {
		value = $bindable(),
		children,
	}: {
		value: string | null;
		children: Snippet;
	} = $props();

	const id = $props.id();

	radioGroupContext.set({
		id,
		get value() {
			return value;
		},
		set value(v) {
			value = v;
		},
	});
</script>

<div class="flex flex-col sm:flex-row sm:gap-6">
	{@render children()}
</div>
