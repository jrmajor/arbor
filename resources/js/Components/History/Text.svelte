<script lang="ts">
	import { type Snippet } from 'svelte';
	import type { Activity } from '@/types/resources/activities';
	import { t } from '@/helpers/translations';

	let { activity, attribute, label }: {
		activity: Activity;
		attribute: string;
		label: string | Snippet;
	} = $props();
</script>

{#if activity.attributes && typeof activity.attributes[attribute] !== 'undefined'}
	<tr class="block w-full md:table-row md:w-auto">
		<td class="block w-full mt-1 -mb-1 md:m-0 pr-4 md:py-1 md:table-cell md:w-auto">
			<strong>
				{#if typeof label === 'string'}
					{t(label)}
				{:else}
					{@render label()}
				{/if}
			</strong>
		</td>

		{#if activity.old}
			<td class="inline pr-4 md:py-1 md:table-cell">
				{#if typeof activity.old[attribute] === 'boolean'}
					{activity.old[attribute] ? t('misc.yes') : t('misc.no')}
				{:else if activity.old[attribute] !== null}
					{activity.old[attribute]}
				{:else}
					<span class="text-gray-500">{t('misc.null')}</span>
				{/if}
			</td>

			<td class="inline pr-4 md:py-1 md:table-cell">=></td>
		{/if}

		<td class="inline md:py-1 md:table-cell">
			{#if typeof activity.attributes[attribute] === 'boolean'}
				{activity.attributes[attribute] ? t('misc.yes') : t('misc.no')}
			{:else if activity.attributes[attribute] !== null}
				{activity.attributes[attribute]}
			{:else}
				<span class="text-gray-500">{t('misc.null')}</span>
			{/if}
		</td>
	</tr>
{/if}
