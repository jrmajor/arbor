<script lang="ts">
	import type { Activity } from '@/types/resources/activities';
	import { t } from '@/helpers/translations';

	let { activity }: { activity: Activity } = $props();
</script>

{#if activity.attributes && typeof activity.attributes.sex !== 'undefined'}
	<tr class="block w-full md:table-row md:w-auto">
		<td class="block w-full mt-1 -mb-1 md:m-0 pr-4 md:py-1 md:table-cell md:w-auto">
			<strong>{t('people.sex')}</strong>
		</td>

		{#if activity.old}
			<td class="inline pr-4 md:py-1 md:table-cell">
				{#if activity.old.sex !== null}
					{activity.old.sex === 'xx' ? t('people.female') : t('people.male')}
				{:else}
					<span class="text-gray-500">{t('misc.null')}</span>
				{/if}
			</td>

			<td class="inline pr-4 md:py-1 md:table-cell">=></td>
		{/if}

		<td class="inline md:py-1 md:table-cell">
			{#if activity.attributes.sex !== null}
				{activity.attributes.sex === 'xx' ? t('people.female') : t('people.male')}
			{:else}
				<span class="text-gray-500">{t('misc.null')}</span>
			{/if}
		</td>
	</tr>
{/if}
