<script lang="ts">
	import type { Snippet } from 'svelte';
	import { randomId } from '@/helpers/utils';
	import { radioGroupContext } from './contexts';

	let {
		value = $bindable(),
		children,
	}: {
		value: string | null;
		children: Snippet;
	} = $props();

	radioGroupContext.set({
		id: randomId(),
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
