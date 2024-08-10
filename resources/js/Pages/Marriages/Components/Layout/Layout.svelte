<script lang="ts">
	import type { Snippet } from 'svelte';
	import type { MarriagePage } from '@/types/resources/marriages';
	import TitleBar from './TitleBar.svelte';
	import SidebarMenu from './SidebarMenu.svelte';

	let { marriage, activeRoute, children }: {
		marriage: MarriagePage;
		children: Snippet;
	} & SharedProps = $props();
</script>

<h1 class="mb-3 leading-none text-3xl font-medium">
	<TitleBar {marriage}/>
</h1>

<div class="flex flex-col md:flex-row space-x-2 space-y-2">
	<main class="grow md:w-1/2 flex flex-col space-y-3">
		{@render children()}
	</main>

	<div class="shrink-0 p-1">
		<SidebarMenu {marriage} {activeRoute}/>
	</div>
</div>
