<script lang="ts">
	import { slide } from 'svelte/transition';
	import type { ShowPersonResource } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import PytlewskiDetails from '../DetailsSections/PytlewskiDetails.svelte';
	import Button from '@/Components/Primitives/Button.svelte';

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
					<strong>{pytlewski.lastName} ({pytlewski.familyName})</strong>
				{:else}
					<strong>{pytlewski.familyName}</strong>
				{/if}
				{pytlewski.name} {pytlewski.middleName}
			</small>
		</a>
		<Button onclick={() => isOpen = !isOpen} outline small>
			{t('people.pytlewski.show_more')}
		</Button>
		{#if isOpen}
			<div transition:slide>
				<PytlewskiDetails {pytlewski}/>
			</div>
		{/if}
	</dd>
{/if}
