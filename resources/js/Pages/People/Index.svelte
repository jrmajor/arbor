<script lang="ts">
	import type { Letters as LettersType } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import Letters from './Components/Letters.svelte';

	let { total, letters }: {
		total: number;
		letters: LettersType;
	} & SharedProps = $props();
</script>

<svelte:head>
	<title>{t('people.titles.index')} - Arbor</title>
</svelte:head>

<main class="p-6 bg-white rounded-lg shadow-sm">
	<Letters {letters}/>

	<hr class="-mx-6 my-5 border-t-2 border-dashed text-gray-200">

	<div class="text-center">
		<small>
			{t('people.index.total')}: <strong>{total}</strong>
		</small>
	</div>
</main>
