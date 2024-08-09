<script lang="ts">
	import type { PersonPage, EditPersonResource } from '@/types/people';
	import TitleBar from './Components/TitleBar.svelte';
	import SidebarMenu from './Components/SidebarMenu.svelte';
	import Form from './Components/Form/Form.svelte';

	let { person }: { person: EditPersonResource & PersonPage } & SharedProps = $props();
</script>

<svelte:head>
	<title>{person.simpleName} - Arbor</title>
</svelte:head>

<h1 class="mb-3 leading-none text-3xl font-medium">
	<TitleBar {person}/>
</h1>

<div class="flex flex-col md:flex-row space-x-2 space-y-2">
	<main class="grow md:w-1/2 p-6 bg-white rounded-lg shadow overflow-hidden">
		<Form {person} action="edit"/>
	</main>

	<div class="shrink-0 p-1">
		<SidebarMenu {person} activePage="edit"/>
	</div>
</div>
