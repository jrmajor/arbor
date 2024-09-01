<script lang="ts" context="module">
	export { default as layout } from './Components/Layout/Layout.svelte';
</script>

<script lang="ts">
	import type { PersonPage } from '@/types/resources/people';
	import type { Activity } from '@/types/resources/activities';
	import { t } from '@/helpers/translations';
	import Link from '@/Components/Primitives/Link.svelte';
	import ParagraphsFromNewlines from '@/Components/ParagraphsFromNewlines.svelte';
	import HistorySex from '@/Components/History/Sex.svelte';
	import HistorySources from '@/Components/History/Sources.svelte';
	import HistoryText from '@/Components/History/Text.svelte';

	let { person, activities }: {
		person: PersonPage;
		activities: Activity[];
	} & SharedProps = $props();
</script>

<svelte:head>
	<title>{person.simpleName} - {t('people.titles.edits_history')} - Arbor</title>
</svelte:head>

{#each activities as activity}
	<div class="p-6 bg-white rounded-lg shadow overflow-hidden">
		{#if activity.description === 'deleted'}
			{t('people.history.deleted')}
		{:else if activity.description === 'restored'}
			{t('people.history.restored')}
		{:else if activity.description === 'changed-visibility'}
			{activity.attributes!.visibility ? t('people.history.made_visible') : t('people.history.made_invisible')}
		{:else if activity.description === 'added-biography'}
			{t('people.history.added-biography')}
			<div class="mt-3 p-4 bg-gray-50 text-gray-700 rounded-md space-y-2 break-words">
				<ParagraphsFromNewlines text={activity.new as unknown as string}/>
			</div>
		{:else if activity.description === 'updated-biography'}
			{t('people.history.updated-biography')}
			<div class="mt-3 p-4 bg-gray-50 text-gray-700 rounded-md space-y-2 break-words">
				<ParagraphsFromNewlines text={activity.new as unknown as string}/>
			</div>
		{:else if activity.description === 'deleted-biography'}
			{t('people.history.deleted-biography')}
		{:else}
			<table class="block md:table">
				<tbody class="block md:table-row-group">
					<HistoryText {activity} attribute="id_pytlewski">
						{#snippet label()}
							{t('people.id_in')}
							<Link href="http://www.pytlewski.pl/index/drzewo/" external>pytlewski.pl</Link>
						{/snippet}
					</HistoryText>
					<HistoryText {activity} attribute="id_wielcy">
						{#snippet label()}
							{t('people.id_in')}
							<Link href="http://www.wielcy.pl/" external>wielcy.pl</Link>
						{/snippet}
					</HistoryText>
					<HistorySex {activity}/>
					<HistoryText {activity} attribute="name" label="people.name"/>
					<HistoryText {activity} attribute="middle_name" label="people.middle_name"/>
					<HistoryText {activity} attribute="family_name" label="people.family_name"/>
					<HistoryText {activity} attribute="last_name" label="people.last_name"/>
					<HistoryText {activity} attribute="birth_date" label="people.birth_date"/>
					<HistoryText {activity} attribute="birth_place" label="people.birth_place"/>
					<HistoryText {activity} attribute="dead" label="people.dead"/>
					<HistoryText {activity} attribute="death_date" label="people.death_date"/>
					<HistoryText {activity} attribute="death_place" label="people.death_place"/>
					<HistoryText {activity} attribute="death_cause" label="people.death_cause"/>
					<HistoryText {activity} attribute="funeral_date" label="people.funeral_date"/>
					<HistoryText {activity} attribute="funeral_place" label="people.funeral_place"/>
					<HistoryText {activity} attribute="burial_date" label="people.burial_date"/>
					<HistoryText {activity} attribute="burial_place" label="people.burial_place"/>
					<HistoryText {activity} attribute="mother_id" label="people.mother"/>
					<HistoryText {activity} attribute="father_id" label="people.father"/>
					<HistorySources {activity}/>
				</tbody>
			</table>
		{/if}

		<div class="-m-6 mt-5 px-6 py-4 bg-gray-50 flex items-center justify-between">
			{activity.causer}
			<small>{activity.datetime}</small>
		</div>
	</div>
{/each}
