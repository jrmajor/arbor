<script lang="ts">
	import { route } from 'ziggy-js';
	import type { Person } from '@/types/resources/people';
	import { inertia } from '@/helpers/inertia';
	import { t } from '@/helpers/translations';

	let {
		person,
		label = null,
		current = false,
	}: {
		person: Person | null;
		label?: string | null;
		current?: boolean;
	} = $props();

	let years = $derived.by(() => {
		if (!person?.visible) return null;

		const years = [
			person.birthYear ? `∗︎${person.birthYear}` : null,
			person.deathYear ? `✝︎${person.deathYear}` : null,
		].filter((y) => y !== null);

		return years.length ? years.join(', ') : null;
	});

	const card = 'relative flex min-h-16 w-full flex-col justify-center rounded-md border px-2 py-1.5 text-center text-sm leading-tight wrap-break-word';
</script>

{#if !person}
	<div class={[card, 'border-dashed border-gray-300 bg-white text-gray-400']}>?</div>
{:else if !person.visible}
	<div class={[card, 'border-gray-300 bg-gray-50 text-gray-500']}>
		{@render badge()}
		<small>[{t('misc.hidden')}]</small>
	</div>
{:else if current}
	<div class={[card, 'border-blue-600 bg-blue-50 ring-1 ring-blue-600']} aria-current="page">
		{@render content(person)}
	</div>
{:else}
	<a
		{@attach inertia()}
		href={route('people.show', person)}
		class={[card, 'border-gray-300 bg-white transition-colors duration-100 hover:border-blue-500 hover:bg-blue-50 focus:border-blue-500 focus:bg-blue-50']}
	>
		{@render content(person)}
	</a>
{/if}

{#snippet badge()}
	{#if label}
		<span class="absolute -top-2 left-1/2 z-1 -translate-x-1/2 rounded-full bg-white px-1.5 text-xs leading-4 whitespace-nowrap text-gray-500">
			{label}
		</span>
	{/if}
{/snippet}

{#snippet content(person: Person & { visible: true })}
	{@render badge()}
	<span class="line-clamp-3" class:italic={person.isDead} class:text-blue-700={!current}>
		{person.name}
		<span class="font-medium">{person.lastName ?? person.familyName}</span>
		{#if person.lastName}
			<span class="text-xs">({person.familyName})</span>
		{/if}
	</span>
	{#if years}
		<span class="mt-0.5 text-xs text-gray-600">{years}</span>
	{/if}
{/snippet}
