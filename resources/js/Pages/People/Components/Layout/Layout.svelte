<script lang="ts">
	import type { Snippet } from 'svelte';
	import type { PersonPage } from '@/types/people';
	import TitleBar from './TitleBar.svelte';
	import SidebarMenu from './SidebarMenu.svelte';

	let { person, activeRoute, children }: {
		person: PersonPage;
		children: Snippet;
	} & SharedProps = $props();

	let perm = $derived(person.perm);
</script>

<h1 class="mb-3 leading-none text-3xl font-medium">
	<TitleBar {person}/>
</h1>

<div class="flex flex-col md:flex-row space-x-2 space-y-2">
	<main class="grow md:w-1/2 flex flex-col space-y-3">
		{@render children()}
	</main>

	{#if perm.update || perm.changeVisibility || perm.delete || perm.restore || perm.viewHistory}
		<div class="shrink-0 p-1">
			<SidebarMenu {person} {activeRoute}/>
		</div>
	{/if}
</div>
