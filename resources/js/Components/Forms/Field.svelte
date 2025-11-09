<script lang="ts">
	import type { Snippet } from 'svelte';
	import Error from './Error.svelte';
	import { setFormFieldContext } from './contexts';

	let { error, children }: {
		error: string | null | undefined;
		children: Snippet;
	} = $props();

	const id = $props.id();

	setFormFieldContext({
		id,
		get error() {
			return error;
		},
	});
</script>

<div class="flex grow basis-full flex-col">
	{@render children()}
	<Error error={error}/>
</div>
