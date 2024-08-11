<script lang="ts">
	import type { Snippet } from 'svelte';
	import SidebarMenu from './SidebarMenu.svelte';

	let { activeRoute, children }: { children: Snippet } & SharedProps = $props();
</script>

<div class="flex flex-col md:flex-row space-x-2 space-y-2">
	{@render children()}

	<div class="shrink-0 p-1">
		<SidebarMenu {activeRoute}/>
	</div>
</div>
