<script lang="ts">
	import { type Snippet } from 'svelte';
	import { slide } from 'svelte/transition';
	import { type RouteList } from 'ziggy-js';
	import Menu from './Menu/Menu.svelte';
	import Flash from './Flash.svelte';

	let { flash, activeRoute, user, children }: {
		flash: FlashData | null;
		activeRoute: keyof RouteList;
		user: SharedUser | null;
		children: Snippet;
	} = $props();

	const { currentYear } = globalThis.arborProps;

	let mailto = $state('');
	$effect(() => {
		mailto = atob('anJoLm1qckBnbWFpbC5jb20=');
	});
</script>

<Menu {activeRoute} {user}/>

<div class="container mx-auto my-1 p-2 pt-5">
	{#if flash}
		<div class="mb-6" transition:slide>
			<Flash {...flash}/>
		</div>
	{/if}

	{@render children()}

	<footer class="m-1 px-3 pt-2 text-center text-gray-600 text-sm">
		&copy; 2018-{currentYear} <a href={`mailto:${mailto}`}>Jeremiasz Major</a>
	</footer>
</div>
