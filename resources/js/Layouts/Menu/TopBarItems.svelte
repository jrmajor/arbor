<script lang="ts">
	import { route, type RouteList } from 'ziggy-js';
	import { hotkey } from '@/helpers/hotkey';
	import { inertia } from '@/helpers/inertia';
	import { t, type Language } from '@/helpers/translations';
	import { useContactEmail } from '@/helpers/useContactEmail.svelte';
	import Icon, { icons } from '@/Components/Icons/Icon.svelte';
	import MenuItem from './MenuItem.svelte';
	import Search from './Search.svelte';
	import { localeName, localeToSwitchTo } from './locale';

	let {
		isMobileOpen = $bindable(),
		appName,
		activeRoute,
		user,
		currentLocale,
	}: {
		isMobileOpen: boolean;
		appName: string;
		activeRoute: keyof RouteList;
		user: SharedUser | null;
		currentLocale: Language;
	} = $props();

	const email = useContactEmail();

	const switcherLocale = $derived(localeToSwitchTo(currentLocale));
</script>

<div class="flex gap-1.5">
	<MenuItem
		{@attach hotkey('t')}
		href={route('people.index')}
		isActive={activeRoute === 'people.index' || activeRoute === 'people.letter'}
	>
		<Icon icon={icons.tree} class="size-4"/>
		{appName}
	</MenuItem>

	<div class="hidden lg:contents">
		{#if user?.canWrite}
			<MenuItem
				href={route('people.create')}
				isActive={activeRoute === 'people.create'}
			>
				<Icon icon={icons.userAdd} class="size-4"/>
				{t('misc.menu.add_person')}
			</MenuItem>
		{/if}

		{#if user?.isSuperAdmin}
			<MenuItem
				href={route('dashboard.users')}
				isActive={activeRoute.startsWith('dashboard')}
			>
				<Icon icon={icons.dashboard} class="size-4"/>
				{t('misc.menu.dashboard')}
			</MenuItem>
		{/if}

		{#if !user}
			<MenuItem href={email.href}>
				<Icon icon={icons.atSymbol} class="size-4"/>
				{t('misc.menu.contact')}
			</MenuItem>

			<MenuItem href={route('login')}>
				<Icon icon={icons.login} class="size-5"/>
				{t('misc.menu.login')}
			</MenuItem>
		{:else}
			<MenuItem
				href={route('settings.edit')}
				isActive={activeRoute === 'settings.edit'}
			>
				<Icon icon={icons.cog} class="size-4"/>
				{t('misc.menu.settings')}
			</MenuItem>

			<MenuItem
				this="button"
				{@attach inertia({ href: route('logout'), method: 'post' })}
			>
				<Icon icon={icons.logout} class="size-5"/>
				{t('misc.menu.logout')}
			</MenuItem>
		{/if}

		<MenuItem
			this="button"
			{@attach inertia({
				href: route('locale.store'),
				method: 'post',
				data: { language: switcherLocale },
			})}
		>
			<Icon icon={icons.translate} class="size-4"/>
			{localeName(switcherLocale)}
		</MenuItem>
	</div>
</div>

<div class="lg:hidden">
	<MenuItem
		this="button"
		onclick={() => isMobileOpen = !isMobileOpen}
		isActive={isMobileOpen}
		class="h-full"
	>
		<svg class="fill-current size-5" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
			{#if isMobileOpen}
				<path d="M10 8.586L2.929 1.515 1.515 2.929 8.586 10l-7.071 7.071 1.414 1.414L10 11.414l7.071 7.071 1.414-1.414L11.414 10l7.071-7.071-1.414-1.414L10 8.586z"/>
			{:else}
				<path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/>
			{/if}
		</svg>
	</MenuItem>
</div>

<div class="hidden lg:contents">
	<Search {user} class="w-96"/>
</div>
