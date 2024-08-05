<script lang="ts">
	import type { Letters as LettersType } from '@/types/people';
	import { t } from '@/helpers/translations';
	import Layout from '@/Layouts/Layout.svelte';
	import Letters from './Components/Letters.svelte';

	let { total, letters, ...rest }: {
		total: number;
		letters: LettersType;
	} & SharedProps = $props();
</script>

<svelte:head>
	<title>{t('people.titles.index')} - Arbor</title>
</svelte:head>

<Layout {...rest}>
	<main class="p-6 bg-white rounded-lg shadow">
		<Letters {letters}/>

		<hr class="-mx-6 my-5 border-t-2 border-dashed">

		<div class="text-center">
			<small>
				{t('people.index.total')}: <strong>{total}</strong>
			</small>
		</div>
	</main>
</Layout>
