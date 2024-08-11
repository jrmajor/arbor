<script lang="ts">
	import { route } from 'ziggy-js';
	import { inertia } from '@inertiajs/svelte';
	import type { PytlewskiRelative } from '@/types/resources/pytlewski';

	type Relative = Omit<PytlewskiRelative, 'surname'> & Partial<Pick<PytlewskiRelative, 'surname'>>;

	let { person }: { person: Relative } = $props();

	let showInternalLink = $derived(person.arborId && person.canBeViewedInArbor);

	let colors = $derived.by(() => {
		if (showInternalLink) return 'text-green-600 hover:text-green-700';
		if (!person.arborId && !person.url) return null;
		if (!person.arborId) return 'text-red-600 hover:text-red-700';
		return 'text-yellow-600 hover:text-yellow-700';
	});
</script>

{#if showInternalLink}
	<a use:inertia href={route('people.show', person.arborId!)} class={colors}>
		{#if person.surname}
			{person.surname},
		{/if}
		{person.name}
	</a>
{:else}
	<!-- href may be null, that's ok, it won't be a link -->
	<a href={person.url} target="_blank" class={colors}>
		{#if person.surname}
			{person.surname},
		{/if}
		{person.name}
	</a>
{/if}
