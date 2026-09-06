<script lang="ts">
	import { tick } from 'svelte';
	import { Tween } from 'svelte/motion';
	import { MediaQuery } from 'svelte/reactivity';
	import { router } from '@inertiajs/svelte';
	import { onClickOutside } from 'runed';
	import { type RouteList } from 'ziggy-js';
	import { type Language } from '@/helpers/translations';
	import MobileItems from './MobileItems.svelte';
	import TopBarItems from './TopBarItems.svelte';

	let { appName, activeRoute, user, currentLocale }: {
		appName: string;
		activeRoute: keyof RouteList;
		user: SharedUser | null;
		currentLocale: Language;
	} = $props();

	let navElement: HTMLElement;

	let isMobileOpen = $state(false);

	let skipDrawerTransition = $state(false);
	let inset = Tween.of(() => isMobileOpen ? 1 : 0, { duration: 200 });
	const isLarge = new MediaQuery('width >= 1024px', false);

	onClickOutside(() => navElement, () => isMobileOpen = false);

	$effect(() => router.on('finish', () => isMobileOpen = false));

	// when viewport is large, collapse mobile menu without transitions
	$effect(() => {
		if (!isLarge.current) return;
		skipDrawerTransition = true;
		inset.set(0, { duration: 0 });
		isMobileOpen = false;
		tick().then(() => skipDrawerTransition = false);
	});
</script>

<!-- hardcoded height prevents shifting the content below when menu expands on mobile -->
<div class="relative h-12.5 z-50">
	<nav
		bind:this={navElement}
		class={[
			'absolute bg-white rounded-lg duration-200 transition-shadow lg:transition-none',
			isMobileOpen ? 'shadow-lg lg:shadow-sm' : 'shadow-sm',
		]}
		style:--inset={inset.current}
	>
		<div class="flex items-stretch justify-between p-1.5">
			<TopBarItems bind:isMobileOpen {activeRoute} {appName} {user} {currentLocale}/>
		</div>

		{#if isMobileOpen}
			<MobileItems {activeRoute} {user} {currentLocale} {skipDrawerTransition}/>
		{/if}
	</nav>
</div>

<style>
	nav {
		--inset-size: calc(var(--inset) * var(--spacing) / 2);
		top: calc(-1 * var(--inset-size));
		left: calc(-1 * var(--inset-size));
		right: calc(-1 * var(--inset-size));
		padding: var(--inset-size);
	}
</style>
