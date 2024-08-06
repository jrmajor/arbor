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
		<!-- svelte-ignore a11y_missing_content -->
		<a href={person.wielcyUrl} target="_blank" class="wielcy" class:missing={!person.wielcyUrl}></a>
		<!-- svelte-ignore a11y_missing_content -->
		<a href={person.pytlewskiUrl} target="_blank" class="pytlewski" class:missing={!person.pytlewskiUrl}></a>
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

<style lang="postcss">
	.wielcy, .pytlewski {
		display: block;
		width: 0.417rem;
		height: 0.417rem;
		border-width: 1px;
	}

	.wielcy {
		background-color: #73adff;
		border-color: #205daf;
	}

	.pytlewski {
		background-color: #79d96a;
		border-color: #208712;
	}

	.wielcy.missing {
		background-color: #f5f8fc;
		border-color: #7ab8ff;
	}

	.pytlewski.missing {
		background-color: #f5f8fc;
		border-color: #7ae16c;
	}
</style>
