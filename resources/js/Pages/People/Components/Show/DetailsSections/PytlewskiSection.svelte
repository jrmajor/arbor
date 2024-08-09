<script lang="ts">
	import { slide } from 'svelte/transition';
	import type { ShowPersonResource } from '@/types/people';
	import { t } from '@/helpers/translations';
	import PytlewskiDetails from '../DetailsSections/PytlewskiDetails.svelte';

	let { person }: { person: ShowPersonResource } = $props();

	let isOpen = $state(false);

	let pytlewski = $derived(person.pytlewski);
</script>

{#if person.pytlewskiId && !pytlewski}
	<dt>{@html t('people.pytlewski.id')}</dt>
	<dd>
		<a href={person.pytlewskiUrl} target="_blank" class="a">
			{person.pytlewskiId}
		</a>
	</dd>
{:else if pytlewski}
	<dt>{@html t('people.pytlewski.id')}</dt>
	<dd>
		<a href={person.pytlewskiUrl} target="_blank" class="a">
			{person.pytlewskiId}
			<small>
				{t('people.pytlewski.as')}
				{#if pytlewski.lastName}
					{pytlewski.lastName} ({pytlewski.familyName})
				{:else}
					{pytlewski.familyName}
				{/if}
				{pytlewski.name} {pytlewski.middleName}
			</small>
		</a>
		<button
			onclick={() => isOpen = !isOpen}
			class="btn-out leading-none text-xs rounded px-2"
		>
			{t('people.pytlewski.show_more')}
		</button>
		{#if isOpen}
			<div transition:slide>
				<PytlewskiDetails {pytlewski}/>
			</div>
		{/if}
	</dd>
{/if}
