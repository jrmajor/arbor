<script lang="ts">
	import { slide } from 'svelte/transition';
	import type { ShowPersonResource } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import Button from '@/Components/Primitives/Button.svelte';
	import Link from '@/Components/Primitives/Link.svelte';
	import PytlewskiDetails from '../DetailsSections/PytlewskiDetails.svelte';

	let { person }: { person: ShowPersonResource } = $props();

	let isOpen = $state(false);

	let pytlewski = $derived(person.pytlewski);
</script>

{#if person.pytlewskiId && !pytlewski}
	<dt>
		{t('people.id_in')}
		<Link href="http://www.pytlewski.pl/index/drzewo/" external>pytlewski.pl</Link>
	</dt>
	<dd>
		<Link href={person.pytlewskiUrl} external>
			{person.pytlewskiId}
		</Link>
	</dd>
{:else if pytlewski}
	<dt>
		{t('people.id_in')}
		<Link href="http://www.pytlewski.pl/index/drzewo/" external>pytlewski.pl</Link>
	</dt>
	<dd>
		<Link href={person.pytlewskiUrl} external>
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
		</Link>
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
