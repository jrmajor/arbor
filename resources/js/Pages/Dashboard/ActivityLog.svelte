<script lang="ts" context="module">
	export { default as layout } from './Components/Layout/Layout.svelte';
</script>

<script lang="ts">
	import { route } from 'ziggy-js';
	import { inertia } from '@inertiajs/svelte';
	import type { ActivityResource } from '@/types/resources/dashboard';
	import { t } from '@/helpers/translations';
	import PaginationLinks from '@/Components/PaginationLinks.svelte';

	let { data: activities, meta }: PaginatedResource<ActivityResource> & SharedProps = $props();
</script>

<svelte:head>
	<title>Activity log - Arbor</title>
</svelte:head>

<main class="grow md:w-1/2 space-y-2 flex flex-col items-center">
	<div class="w-full p-6 bg-white rounded-lg shadow">
		<table>
			<tbody>
				{#each activities as activity (activity.id)}
					<tr>
						<td class="tabular-nums p-1">
							{activity.datetime}
						</td>

						<td class="p-1">
							{#if activity.logName === 'people'}
								<a use:inertia href={route('people.history', activity.subjectId)} class="a">
									{t('people.person')} №{activity.subjectId}
								</a>
							{:else if activity.logName === 'marriages'}
								<a use:inertia href={route('marriages.history', activity.subjectId)} class="a">
									{t('marriages.marriage')} №{activity.subjectId}
								</a>
							{:else if activity.logName === 'users'}
								{t('users.user')} №{activity.subjectId}
							{/if}

							{t(`activities.${activity.description}`)}

							{#if activity.causer}
								{t('activities.by')} <strong>{activity.causer}</strong>
							{/if}
						</td>
					</tr>
				{/each}
			</tbody>
		</table>
	</div>

	<PaginationLinks {meta}/>
</main>
