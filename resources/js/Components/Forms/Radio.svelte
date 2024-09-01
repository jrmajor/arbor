<script lang="ts">
	import type { Snippet } from 'svelte';
	import { randomId } from '@/helpers/utils';
	import { radioGroupContext } from './contexts';

	let { value, children }: {
		value: string | null;
		children: Snippet;
	} = $props();

	const id = randomId();

	const radioContext = radioGroupContext.get();

	let checked = $state(radioContext.value === value);

	$effect(() => {
		checked = radioContext.value === value;
	});

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
		class="border-gray-300 focus:border-blue-600 focus:ring focus:ring-blue-500/50 focus:ring-offset-0"
		{onchange}
	>
	<label class="ml-2" for={id}>
		{@render children()}
	</label>
</div>
