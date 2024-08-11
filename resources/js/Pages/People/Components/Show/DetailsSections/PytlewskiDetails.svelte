<script lang="ts">
	import type { Pytlewski } from '@/types/resources/pytlewski';
	import { t } from '@/helpers/translations';
	import PytlewskiRelative from './PytlewskiRelative.svelte';

	let { pytlewski }: { pytlewski: Pytlewski } = $props();
</script>

<small class="block -mt-0.5 ml-4 leading-tight">
	{#if pytlewski.mother || pytlewski.father}
		<p class="mt-1.5">{t('people.pytlewski.parents')}:</p>
		{#if pytlewski.mother}
			<p class="ml-4"><PytlewskiRelative person={pytlewski.mother}/></p>
		{/if}
		{#if pytlewski.father}
			<p class="ml-4"><PytlewskiRelative person={pytlewski.father}/></p>
		{/if}
	{/if}

	{#if pytlewski.marriages.length}
		<p class="mt-1.5">{t('people.pytlewski.marriages')}:</p>
		{#each pytlewski.marriages as marriage}
			<p class="ml-4"><PytlewskiRelative person={marriage}/></p>
		{/each}
	{/if}

	{#if pytlewski.children.length}
		<p class="mt-1.5">{t('people.pytlewski.children')}:</p>
		<p class="ml-4">
			{#each pytlewski.children as child, i}
				{i > 0 ? ', ' : ''}
				<PytlewskiRelative person={child}/>
			{/each}
		</p>
	{/if}

	{#if pytlewski.siblings.length}
		<p class="mt-1.5">{t('people.pytlewski.siblings')}:</p>
		<p class="ml-4">
			{#each pytlewski.siblings as sibling, i}
				{i > 0 ? ', ' : ''}
				<PytlewskiRelative person={sibling}/>
			{/each}
		</p>
	{/if}
</small>
