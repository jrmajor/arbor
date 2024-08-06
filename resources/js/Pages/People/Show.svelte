<script lang="ts">
	import type { ShowPersonResource } from '@/types/people';
	import Layout from '@/Layouts/Layout.svelte';
	import TitleBar from './Components/TitleBar.svelte';
	import SidebarMenu from './Components/SidebarMenu.svelte';
	import Details from './Components/Show/Sections/Details.svelte';
	import Biography from './Components/Show/Sections/Biography.svelte';
	import SmallTree from './Components/Show/Sections/SmallTree.svelte';

	let { person, ...rest }: { person: ShowPersonResource } & SharedProps = $props();

	let perm = $derived(person.perm);
</script>

<svelte:head>
	<title>{person.simpleName} - Arbor</title>
</svelte:head>

<Layout {...rest}>
	<h1 class="mb-3 leading-none text-3xl font-medium">
		<TitleBar {person}/>
	</h1>

	<div class="flex flex-col md:flex-row space-x-2 space-y-2">
		<main class="grow md:w-1/2 flex flex-col space-y-3">
			<Details {person}/>
			<Biography {person}/>
			<SmallTree {person}/>
		</main>

		{#if perm.update || perm.changeVisibility || perm.delete || perm.restore || perm.viewHistory}
			<div class="shrink-0 p-1">
				<SidebarMenu {person} activePage="show"/>
			</div>
		{/if}
	</div>
</Layout>
