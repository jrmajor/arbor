<script lang="ts">
	import type { Snippet } from 'svelte';
	import SidebarMenu from './SidebarMenu.svelte';

	let { activeRoute, children }: { children: Snippet } & SharedProps = $props();
</script>

<div class="flex flex-col gap-2 md:flex-row">
	{@render children()}

	<div class="shrink-0 p-1 md:py-3">
		<SidebarMenu {activeRoute}/>
	</div>
</div>
