<script lang="ts">
	import type { Snippet } from 'svelte';
	import { randomId } from '@/helpers/utils';
	import Error from './Error.svelte';
	import { formFieldContext } from './contexts';

	let { error, children }: {
		error: string | null | undefined;
		children: Snippet;
	} = $props();

	formFieldContext.set({
		id: randomId(),
		get error() {
			return error;
		},
	});
</script>

<div class="flex grow basis-full flex-col">
	{@render children()}
	<Error error={error}/>
</div>
