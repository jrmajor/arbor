<script lang="ts">
	import { slide } from 'svelte/transition';
	import type { ShowPersonResource } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import Button from '@/Components/Primitives/Button.svelte';
	import Link from '@/Components/Primitives/Link.svelte';
	import WielcyDetails from './WielcyDetails.svelte';

	let { person }: { person: ShowPersonResource } = $props();

	let isOpen = $state(false);

	let wielcy = $derived(person.wielcy);
</script>

{#if person.wielcyId && !wielcy}
	<dt>
		{t('people.id_in')}
		<Link href="http://www.wielcy.pl/" external>wielcy.pl</Link>
	</dt>
	<dd>
		<Link href={person.wielcyUrl} external>
			{person.wielcyId}
		</Link>
	</dd>
{:else if wielcy}
	<dt>
		{t('people.id_in')}
		<Link href="http://www.wielcy.pl/" external>wielcy.pl</Link>
	</dt>
	<dd>
		<Link href={person.wielcyUrl} external>
			{person.wielcyId}
			<small>
				{t('people.wielcy.as')}
				<strong>{wielcy.surname}</strong>
				{wielcy.name} {wielcy.middleName}
			</small>
		</Link>
		{#if wielcy.mother || wielcy.father}
			<Button onclick={() => isOpen = !isOpen} outline small>
				{t('people.wielcy.show_more')}
			</Button>
			{#if isOpen}
				<div transition:slide>
					<WielcyDetails {wielcy}/>
				</div>
			{/if}
		{/if}
	</dd>
{/if}
