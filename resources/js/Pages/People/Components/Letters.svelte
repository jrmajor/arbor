<script lang="ts">
	import { route } from 'ziggy-js';
	import type { Letters } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import Link from '@/Components/Primitives/Link.svelte';

	let {
		letters,
		activeType = null,
		activeLetter = null,
	}: {
		letters: Letters;
		activeType?: 'f' | 'l' | null;
		activeLetter?: string | null;
	} = $props();
</script>

<div>
	<h2>{t('people.index.by_family_name')}:</h2>

	<ul class="columns-3 xs:columns-4 sm:columns-5 md:columns-6 lg:columns-8">
		{#each letters.family as letter}
			<li class:font-bold={activeType === 'f' && activeLetter === letter.letter}>
				<Link href={route('people.letter', { type: 'f', letter: encodeURIComponent(letter.letter) })}>
					{letter.letter}
					<small>[{letter.count}]</small>
				</Link>
			</li>
		{/each}
	</ul>
</div>

<div class="mt-4">
	<h2>{t('people.index.by_last_name')}:</h2>

	<ul class="columns-3 xs:columns-4 sm:columns-5 md:columns-6 lg:columns-8">
		{#each letters.last as letter}
			<li class:font-bold={activeType === 'l' && activeLetter === letter.letter}>
				<Link href={route('people.letter', { type: 'l', letter: encodeURIComponent(letter.letter) })}>
					{letter.letter}
					<small>[{letter.count}]</small>
				</Link>
			</li>
		{/each}
	</ul>
</div>
