<script lang="ts">
	import { type Snippet } from 'svelte';
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
	$effect(() => {
		// setting it in effect should prevent it from being rendered on server
		mailto = atob('anJoLm1qckBnbWFpbC5jb20=');
	});
</script>

<Menu {activeRoute} {user} {currentLocale} {availableLocales}/>

<div class="container mx-auto my-1 p-2 pt-5">
	<FlashMessages {flash}/>

	{@render children()}

	<footer class="m-1 px-3 pt-2 text-center text-gray-600 text-sm">
		&copy; 2018-{currentYear}
		<a href={`mailto:${mailto}`} class="hover:text-gray-900">Jeremiasz Major</a>
	</footer>
</div>
