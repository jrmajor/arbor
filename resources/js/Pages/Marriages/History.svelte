<script lang="ts" module>
	export { default as layout } from './Components/Layout/Layout.svelte';
</script>

<script lang="ts">
	import type { Activity } from '@/types/resources/activities';
	import { t } from '@/helpers/translations';
	import HistoryEnum from '@/Components/History/Enum.svelte';
	import HistoryText from '@/Components/History/Text.svelte';

	let { activities }: { activities: Activity[] } & SharedProps = $props();
</script>

<svelte:head>
	<title>{t('marriages.titles.marriage_edits_history')} - Arbor</title>
</svelte:head>

{#each activities as activity}
	<div class="p-6 bg-white rounded-lg shadow-sm overflow-hidden">
		{#if activity.description === 'deleted'}
			{t('marriages.history.deleted')}
		{:else if activity.description === 'restored'}
			{t('marriages.history.restored')}
		{:else}
			<table class="block md:table">
				<tbody class="block md:table-row-group">
					<HistoryText {activity} attribute="woman_id" label="marriages.woman"/>
					<HistoryText {activity} attribute="woman_order" label="marriages.woman_order"/>
					<HistoryText {activity} attribute="man_id" label="marriages.man"/>
					<HistoryText {activity} attribute="man_order" label="marriages.man_order"/>
					<HistoryEnum {activity} attribute="rite" label="marriages.rite" translationsKey="marriages.rites"/>
					<HistoryEnum {activity} attribute="first_event_type" label="marriages.first_event_type" translationsKey="marriages.event_types"/>
					<HistoryText {activity} attribute="first_event_date" label="marriages.first_event_date"/>
					<HistoryText {activity} attribute="first_event_place" label="marriages.first_event_place"/>
					<HistoryEnum {activity} attribute="second_event_type" label="marriages.second_event_type" translationsKey="marriages.event_types"/>
					<HistoryText {activity} attribute="second_event_date" label="marriages.second_event_date"/>
					<HistoryText {activity} attribute="second_event_place" label="marriages.second_event_place"/>
					<HistoryText {activity} attribute="divorced" label="marriages.divorced"/>
					<HistoryText {activity} attribute="divorce_date" label="marriages.divorce_date"/>
					<HistoryText {activity} attribute="divorce_place" label="marriages.divorce_place"/>
				</tbody>
			</table>
		{/if}

		<div class="-m-6 mt-5 px-6 py-4 bg-gray-50 flex items-center justify-between">
			{activity.causer}
			<small>{activity.datetime}</small>
		</div>
	</div>
{/each}
