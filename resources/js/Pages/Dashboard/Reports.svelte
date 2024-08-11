<script lang="ts" context="module">
	export { default as layout } from './Components/Layout/Layout.svelte';
</script>

<script lang="ts">
	import type { Person } from '@/types/people';
	import Name from '@/Components/Name.svelte';

	let { shouldBeDead, visibleAlive, invisibleDead }: {
		shouldBeDead: Person[];
		visibleAlive: Person[];
		invisibleDead: Person[];
	} & SharedProps = $props();
</script>

<svelte:head>
	<title>Reports - Arbor</title>
</svelte:head>

<main class="grow md:w-1/2 space-y-5">
	{#if shouldBeDead.length}
		<div>
			<h2 class="mb-2 leading-none text-xl font-medium">Should be dead</h2>
			<ul class="w-full p-6 bg-white rounded-lg shadow">
				{#each shouldBeDead as person}
					<li><Name {person}/></li>
				{/each}
			</ul>
		</div>
	{/if}

	{#if visibleAlive.length}
		<div>
			<h2 class="mb-2 leading-none text-xl font-medium">Visible alive</h2>
			<ul class="w-full p-6 bg-white rounded-lg shadow">
				{#each visibleAlive as person}
					<li><Name {person}/></li>
				{/each}
			</ul>
		</div>
	{/if}

	{#if invisibleDead.length}
		<div>
			<h2 class="mb-2 leading-none text-xl font-medium">Invisible dead</h2>
			<ul class="w-full p-6 bg-white rounded-lg shadow">
				{#each invisibleDead as person}
					<li><Name {person}/></li>
				{/each}
			</ul>
		</div>
	{/if}
</main>
