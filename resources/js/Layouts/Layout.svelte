<script lang="ts">
	import type { Snippet } from 'svelte';
	import { useContactEmail } from '@/helpers/useContactEmail.svelte';
	import FlashMessages from './FlashMessages.svelte';
	import Menu from './Menu/Menu.svelte';

	let {
		currentYear,
		currentLocale,
		flash,
		activeRoute,
		user,
		children,
	}: { children: Snippet } & SharedProps = $props();

	const email = useContactEmail();
</script>

<Menu {activeRoute} {user} {currentLocale}/>

<div class="container mx-auto my-1 flex flex-col gap-3">
	<FlashMessages {flash}/>

	{@render children()}

	<footer class="mb-1 px-3 text-center text-gray-600 text-sm">
		&copy; 2018-{currentYear}
		<a href={email.href} class="hover:text-gray-900">Jeremiasz Major</a>
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
