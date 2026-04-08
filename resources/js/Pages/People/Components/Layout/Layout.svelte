<script lang="ts">
	import type { Snippet } from 'svelte';
	import type { PersonPage } from '@/types/resources/people';
	import PageTitle from '@/Components/PageTitle.svelte';
	import SidebarMenu from './SidebarMenu.svelte';
	import TitleBar from './TitleBar.svelte';

	let { person, activeRoute, children }: {
		person: PersonPage;
		children: Snippet;
	} & SharedProps = $props();

	let perm = $derived(person.perm);
</script>

<PageTitle>
	<TitleBar {person}/>
</PageTitle>

<div class="flex flex-col gap-2 md:flex-row">
	<main class="grow md:w-1/2 flex flex-col gap-3">
		{@render children()}
	</main>

	{#if perm.update || perm.changeVisibility || perm.delete || perm.restore || perm.viewHistory}
		<div class="shrink-0 p-1 md:py-3">
			<SidebarMenu {person} {activeRoute}/>
		</div>
	{/if}
</div>
