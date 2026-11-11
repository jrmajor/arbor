<script lang="ts" module>
	export { default as layout } from './Components/Layout/Layout.svelte';
</script>

<script lang="ts">
	import type { Person, PersonPage, Relationship } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import RelationshipPicker from './Components/Relationship/RelationshipPicker.svelte';
	import { kinshipName, shortName } from './Components/Relationship/kinship';
	import Card from './Components/Show/FamilyTree/Card.svelte';

	let { person, other, relationship }: {
		person: PersonPage;
		other: Person | null;
		relationship: Relationship | null;
	} & SharedProps = $props();

	let ancestorIds = $derived(new Set(relationship?.commonAncestors.map((p) => p.id)));
</script>

<svelte:head>
	<title>{person.simpleName} - {t('relationship.title')} - Arbor</title>
</svelte:head>

<div class="p-6 bg-white rounded-lg shadow-sm">
	<p class="mb-2 text-sm text-gray-600">{t('relationship.check_with')}</p>
	<RelationshipPicker personId={person.id} value={other?.id ?? null}/>
</div>

{#if other}
	<div class="p-6 bg-white rounded-lg shadow-sm">
		{#if !relationship}
			<p class="text-gray-600">{t('relationship.not_found')}</p>
		{:else}
			<p class="text-sm text-gray-500">{person.simpleName} → {shortName(other)}</p>
			<p class="mt-1 text-2xl font-medium">{kinshipName(relationship.kinship)}</p>
			<p class="mt-1 text-sm text-gray-600">
				{#if !relationship.blood}
					{t('relationship.by_marriage')}
				{:else if relationship.commonAncestors.length}
					{t('relationship.common_ancestors', { count: relationship.commonAncestors.length })}:
					{relationship.commonAncestors.map(shortName).join(` ${t('relationship.and')} `)}
				{/if}
			</p>

			<ol class="mt-6 flex flex-col items-start">
				{#each relationship.steps as step, i (i)}
					{#if step.step}
						<li class="ml-8 border-l-2 border-gray-200 py-1.5 pl-3 text-xs text-gray-500">
							{t(`relationship.step.${step.step}`, { sex: step.sex ?? 'unknown' })}
						</li>
					{/if}
					<li class="w-60 max-w-full pt-2">
						<Card
							person={step.person}
							current={i === 0}
							label={ancestorIds.has(step.person.id) ? t('relationship.common_ancestor') : null}
						/>
					</li>
				{/each}
			</ol>
		{/if}
	</div>
{/if}
