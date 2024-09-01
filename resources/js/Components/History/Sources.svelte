<script lang="ts">
	import type { Activity } from '@/types/resources/activities';
	import { t } from '@/helpers/translations';
	import Source from '@/Components/Source.svelte';

	let { activity }: { activity: Activity } = $props();
</script>

{#if activity.attributes && typeof activity.attributes.sources !== 'undefined'}
	<tr class="block w-full md:table-row md:w-auto">
		<td class="block w-full mt-1 -mb-1 md:m-0 pr-4 md:py-1 md:table-cell md:w-auto">
			<strong>{t('people.sources')}</strong>
		</td>

		{#if activity.old}
			<td class="inline pr-4 md:py-1 md:table-cell">
				{#if Array.isArray(activity.old.sources) && activity.old.sources.length}
					{#each activity.old.sources as source, i}
						{i > 0 ? ', ' : ''}
						<Source html={source}/>
					{/each}
				{:else}
					<span class="text-gray-500">{t('misc.null')}</span>
				{/if}
			</td>

			<td class="inline pr-4 md:py-1 md:table-cell">=></td>
		{/if}

		<td class="inline md:py-1 md:table-cell">
			{#if Array.isArray(activity.attributes.sources) && activity.attributes.sources.length}
				{#each activity.attributes.sources as source, i}
					{i > 0 ? ', ' : ''}
					<Source html={source}/>
				{/each}
			{:else}
				<span class="text-gray-500">{t('misc.null')}</span>
			{/if}
		</td>
	</tr>
{/if}
