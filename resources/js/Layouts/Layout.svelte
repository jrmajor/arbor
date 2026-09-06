<script lang="ts">
	import type { Snippet } from 'svelte';
	import { useContactEmail } from '@/helpers/useContactEmail.svelte';
	import FlashMessages from './FlashMessages.svelte';
	import Menu from './Menu/Menu.svelte';

	let {
		appName,
		currentYear,
		currentLocale,
		flash,
		activeRoute,
		user,
		children,
	}: { children: Snippet } & SharedProps = $props();

	const email = useContactEmail();
</script>

<div class="container mx-auto flex flex-col gap-3">
	<Menu {appName} {activeRoute} {user} {currentLocale}/>

	<FlashMessages {flash}/>

	{@render children()}

	<footer class="px-3 text-center text-gray-600 text-sm">
		&copy; 2018-{currentYear}
		<a href={email.href} class="hover:text-gray-900">Jeremiasz Major</a>
	</footer>
</div>

<style>
	.container {
		padding:
			max(env(safe-area-inset-top), calc(3 * var(--spacing)))
			max(env(safe-area-inset-right), 0.5rem)
			max(env(safe-area-inset-bottom), calc(3 * var(--spacing)))
			max(env(safe-area-inset-left), 0.5rem);
	}
</style>
