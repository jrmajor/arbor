<script lang="ts">
	import type { Snippet } from 'svelte';
	import type { MarriagePage } from '@/types/resources/marriages';
	import PageTitle from '@/Components/PageTitle.svelte';
	import SidebarMenu from './SidebarMenu.svelte';
	import TitleBar from './TitleBar.svelte';

	let { marriage, activeRoute, children }: {
		marriage: MarriagePage;
		children: Snippet;
	} & SharedProps = $props();
</script>

<PageTitle>
	<TitleBar {marriage}/>
</PageTitle>

<div class="flex flex-col gap-2 md:flex-row">
	<main class="grow md:w-1/2 flex flex-col gap-3">
		{@render children()}
	</main>

	<div class="shrink-0 p-1 md:py-3">
		<SidebarMenu {marriage} {activeRoute}/>
	</div>
</div>
