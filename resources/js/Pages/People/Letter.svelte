<script lang="ts">
	import type { Letters as LettersType, Person } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import Name from '@/Components/Name.svelte';
	import Letters from './Components/Letters.svelte';

	let { people, letters, activeType, activeLetter }: {
		people: Person[];
		letters: LettersType;
		activeType: 'f' | 'l' | null;
		activeLetter: string | null;
	} & SharedProps = $props();
</script>

<svelte:head>
	<title>{t('people.titles.index')} - Arbor</title>
</svelte:head>

<main class="p-6 bg-white rounded-lg shadow">
	<Letters {letters} {activeType} {activeLetter}/>

	<hr class="-mx-6 my-5 border-t-2 border-dashed text-gray-200">

	<ul>
		{#each people as person}
			<li><Name {person} bold={activeType}/></li>
		{/each}
	</ul>
</main>
