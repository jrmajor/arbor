<script lang="ts">
	import { route } from 'ziggy-js';
	import type { Person } from '@/types/people';
	import { t } from '@/helpers/translations';

	let {
		person,
		bold = null,
		showYears = true,
	}: {
		person: Person;
		bold?: 'f' | 'l' | null;
		showYears?: boolean;
	} = $props();
</script>

{#if person.wielcyUrl || person.pytlewskiUrl}
	<div class="inline-block space-y-[0.139rem]">
		{#if person.wielcyUrl}
			<!-- svelte-ignore a11y_missing_content -->
			<a
				class="block w-[0.417rem] h-[0.417rem] bg-[#73adff] border border-[#205daf]"
				href={person.wielcyUrl}
				target="_blank"
			></a>
		{:else}
			<span class="block w-[0.417rem] h-[0.417rem] bg-[#f5f8fc] border border-[#7ab8ff]"></span>
		{/if}
		{#if person.pytlewskiUrl}
			<!-- svelte-ignore a11y_missing_content -->
			<a
				class="block w-[0.417rem] h-[0.417rem] bg-[#79d96a] border border-[#208712]"
				href={person.pytlewskiUrl}
				target="_blank"
			></a>
		{:else}
			<span class="block w-[0.417rem] h-[0.417rem] bg-[#f5f8fc] border border-[#7ae16c]"></span>
		{/if}
	</div>
{/if}

{#if person.visible}
	<a href={route('people.show', person)} class="a" class:italic={person.isDead}>
		{person.name}
		{#if person.lastName}
			<span class:font-semibold={bold === 'l'}>{person.lastName}</span>
			(<span class:font-semibold={bold === 'f'}>{person.familyName}</span>)
		{:else}
			<span class:font-semibold={bold}>{person.familyName}</span>
		{/if}
		{#if showYears}
			{#if person.birthYear && person.deathYear}
				(&#8727;&#xFE0E;{person.birthYear}, &#10013;&#xFE0E;{person.deathYear})
			{:else if person.birthYear}
				(&#8727;&#xFE0E;{person.birthYear})
			{:else if person.deathYear}
				(&#10013;&#xFE0E;{person.deathYear})
			{/if}
		{/if}
	</a>
{:else}
	<small>[{t('misc.hidden')}]</small>
{/if}

{#if person.canBeEdited}
	<a href={route('people.edit', person)} class="a">
		<small>[№{person.id}]</small>
	</a>
{/if}
