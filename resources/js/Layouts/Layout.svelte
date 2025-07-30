<script lang="ts">
	import { onMount, type Snippet } from 'svelte';
	import FlashMessages from './FlashMessages.svelte';
	import Menu from './Menu/Menu.svelte';

	let {
		currentYear,
		currentLocale,
		availableLocales,
		flash,
		activeRoute,
		user,
		children,
	}: { children: Snippet } & SharedProps = $props();

	let mailto = $state('');
	onMount(() => {
		// setting it in effect should prevent it from being rendered on server
		mailto = atob('anJoLm1qckBnbWFpbC5jb20=');
	});
</script>

<Menu {activeRoute} {user} {currentLocale} {availableLocales}/>

<div class="container mx-auto my-1">
	<FlashMessages {flash}/>

	{@render children()}

	<footer class="mt-3 mb-1 px-3 text-center text-gray-600 text-sm">
		&copy; 2018-{currentYear}
		<a href={`mailto:${mailto}`} class="hover:text-gray-900">Jeremiasz Major</a>
	</footer>
</div>

<style>
	.container {
		padding:
			1.25rem
			max(env(safe-area-inset-right), 0.5rem)
			max(env(safe-area-inset-bottom), 0.5rem)
			max(env(safe-area-inset-left), 0.5rem);
	}
</style>
