<script lang="ts">
	import { slide } from 'svelte/transition';
	import { route, type RouteList } from 'ziggy-js';
	import { inertia } from '@/helpers/inertia';
	import { t, type Language } from '@/helpers/translations';
	import { useContactEmail } from '@/helpers/useContactEmail.svelte';
	import Icon, { icons } from '@/Components/Icons/Icon.svelte';
	import MenuItem from './MenuItem.svelte';
	import Search from './Search.svelte';
	import { localeName, localeToSwitchTo } from './locale';

	let {
		activeRoute,
		user,
		currentLocale,
		skipDrawerTransition,
	}: {
		activeRoute: keyof RouteList;
		user: SharedUser | null;
		currentLocale: Language;
		skipDrawerTransition: boolean;
	} = $props();

	const email = useContactEmail();

	const switcherLocale = $derived(localeToSwitchTo(currentLocale));
</script>

<div
	transition:slide={{ duration: skipDrawerTransition ? 0 : 400 }}
	class="flex flex-col p-1.5 pt-0 gap-1"
>
	<Search {user} class="mt-0.5"/>

	{#if user?.canWrite}
		<MenuItem
			href={route('people.create')}
			isActive={activeRoute === 'people.create'}
			mobile
		>
			<Icon icon={icons.userAdd} class="size-4"/>
			{t('misc.menu.add_person')}
		</MenuItem>
	{/if}

	{#if user?.isSuperAdmin}
		<MenuItem
			href={route('dashboard.users')}
			isActive={activeRoute.startsWith('dashboard')}
			mobile
		>
			<Icon icon={icons.dashboard} class="size-4"/>
			{t('misc.menu.dashboard')}
		</MenuItem>
	{/if}

	{#if !user}
		<MenuItem href={email.href} mobile>
			<Icon icon={icons.atSymbol} class="size-4"/>
			{t('misc.menu.contact')}
		</MenuItem>

		<MenuItem href={route('login')} mobile>
			<Icon icon={icons.login} class="size-5"/>
			{t('misc.menu.login')}
		</MenuItem>
	{:else}
		<MenuItem
			href={route('settings.edit')}
			isActive={activeRoute === 'settings.edit'}
			mobile
		>
			<Icon icon={icons.cog} class="size-4"/>
			{t('misc.menu.settings')}
		</MenuItem>

		<MenuItem
			this="button"
			{@attach inertia({ href: route('logout'), method: 'post' })}
			mobile
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
		mobile
	>
		<Icon icon={icons.translate} class="size-4"/>
		{localeName(switcherLocale)}
	</MenuItem>
</div>
