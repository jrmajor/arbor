<script lang="ts">
	import { onMount } from 'svelte';
	import { scale } from 'svelte/transition';
	import { inertia, router } from '@inertiajs/svelte';
	import { route, type RouteList } from 'ziggy-js';
	import { hotkey } from '@/helpers/hotkey';
	import { t, type Language } from '@/helpers/translations';
	import { useContactEmail } from '@/helpers/useContactEmail.svelte';
	import Icon, { icons } from '@/Components/Icons/Icon.svelte';
	import Button from '@/Components/Primitives/Button.svelte';
	import Search from './Search.svelte';

	let { activeRoute, user, currentLocale, availableLocales }: {
		activeRoute: keyof RouteList;
		user: SharedUser | null;
		currentLocale: Language;
		availableLocales: Language[];
	} = $props();

	let containerElement: HTMLElement;
	// svelte-ignore non_reactive_update
	let dropdownElement: HTMLElement;

	let email = useContactEmail();

	let open = $state(false);
	let dropdown = $state(false);

	function clickOutside(event: MouseEvent) {
		if (open && !containerElement.contains(event.target as Node)) open = false;
		if (dropdown && !dropdownElement.contains(event.target as Node)) dropdown = false;
	}

	onMount(() => {
		router.on('start', () => {
			dropdown = false;
		});
		router.on('finish', () => {
			open = false;
		});
	});
</script>

<svelte:document onclick={clickOutside}/>

<nav bind:this={containerElement} class="mb-1 bg-white shadow-md">
	<div class="container mx-auto">
		<div class="menu-padding flex items-center justify-between flex-wrap">
			<div class="flex items-center">
				<a
					{@attach hotkey('t')}
					use:inertia
					href={route('people.index')}
					class="px-4 pt-4 pb-3 md:pt-5 md:pb-4 lg:pt-6 lg:pb-4 text-gray-800
						hover:text-gray-900 hover:bg-gray-100 focus:bg-cool-gray-100
						border-b-2 border-solid
						{activeRoute === 'people.index' || activeRoute === 'people.letter' ? 'border-blue-500' : 'border-transparent hover:border-gray-400 focus:border-gray-400 active:border-blue-500'}
						focus:outline-none hover:no-underline
						transition-colors duration-200
						flex items-center"
				>
					<Icon icon={icons.tree} class="size-4 mr-2"/>
					{t('misc.menu.tree')}
				</a>
			</div>

			<button
				onclick={() => open = !open}
				type="button"
				class="block lg:hidden px-4 pt-4 pb-3 md:pt-5 md:pb-4 -my-2 text-gray-800 hover:text-gray-900 focus:outline-none"
			>
				<svg class="fill-current size-5" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
					{#if open}
						<path d="M10 8.586L2.929 1.515 1.515 2.929 8.586 10l-7.071 7.071 1.414 1.414L10 11.414l7.071 7.071 1.414-1.414L11.414 10l7.071-7.071-1.414-1.414L10 8.586z"/>
					{:else}
						<path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/>
					{/if}
				</svg>
			</button>

			<div
				class={[
					'flex-col pb-2 mt-2 w-full lg:flex! lg:flex-row lg:w-auto lg:mt-0 lg:pb-0 lg:items-center',
					open ? 'flex' : 'hidden',
				]}
			>
				<Search {user}/>

				{#if user?.canWrite}
					<a
						use:inertia
						href={route('people.create')}
						class="px-3 py-1 lg:pt-6 lg:pb-4 text-gray-800
							hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 lg:hover:bg-gray-100 lg:focus:bg-cool-gray-100
							rounded lg:rounded-none uppercase lg:normal-case
							border-b-2 border-solid
							{activeRoute === 'people.create' ? 'lg:border-blue-500' : 'border-transparent lg:hover:border-gray-400 lg:focus:border-gray-400 lg:active:border-blue-500'}
							focus:outline-none hover:no-underline
							transition-colors duration-200"
					>
						<div class="w-full {activeRoute === 'people.create' ? 'border-b-2 border-dotted border-blue-500 lg:border-none' : ''} flex items-center">
							<Icon icon={icons.userAdd} class="size-4 mr-2 lg:hidden"/>
							{t('misc.menu.add_person')}
						</div>
					</a>
				{/if}

				{#if user?.isSuperAdmin}
					<a
						href={route('dashboard.users')}
						class="px-3 py-1 lg:pt-6 lg:pb-4 text-gray-800
							hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 lg:hover:bg-gray-100 lg:focus:bg-cool-gray-100
							rounded lg:rounded-none uppercase lg:normal-case
							border-b-2 border-solid
							{activeRoute.startsWith('dashboard') ? 'lg:border-blue-500' : 'border-transparent lg:hover:border-gray-400 lg:focus:border-gray-400 lg:active:border-blue-500'}
							focus:outline-none hover:no-underline
							transition-colors duration-200"
					>
						<div class="w-full {activeRoute.startsWith('dashboard') ? 'border-b-2 border-dotted border-blue-500 lg:border-none' : ''} flex items-center">
							<Icon icon={icons.dashboard} class="size-4 mr-2 lg:hidden"/>
							{t('misc.menu.dashboard')}
						</div>
					</a>
				{/if}

				{#if !user}
					<a
						href={email.href}
						class="px-3 py-1 lg:pt-6 lg:pb-4 text-gray-800
							hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 lg:hover:bg-gray-100 lg:focus:bg-cool-gray-100
							rounded lg:rounded-none uppercase lg:normal-case
							border-b-2 border-solid border-transparent
							lg:hover:border-gray-400 lg:focus:border-gray-400 lg:active:border-blue-500
							focus:outline-none hover:no-underline
							transition-colors duration-200
							flex items-center"
					>
						<Icon icon={icons.atSymbol} class="size-4 mr-2 lg:hidden"/>
						{t('misc.menu.contact')}
					</a>

					<a
						href={route('login')}
						class="px-3 py-1 lg:pt-6 lg:pb-4 text-gray-800
							hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 lg:hover:bg-gray-100 lg:focus:bg-cool-gray-100
							rounded lg:rounded-none uppercase lg:normal-case
							border-b-2 border-solid border-transparent
							lg:hover:border-gray-400 lg:focus:border-gray-400 lg:active:border-blue-500
							focus:outline-none hover:no-underline
							transition-colors duration-200
							flex items-center"
					>
						<Icon icon={icons.login} class="size-5 -ml-1 mr-1 lg:hidden"/>
						{t('misc.menu.login')}
					</a>
				{:else}
					<a
						{@attach hotkey('g s')}
						use:inertia
						href={route('settings.edit')}
						class="lg:hidden px-3 py-1 text-gray-800
							hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-gray-100
							rounded uppercase
							border-b-2 border-solid border-transparent
							focus:outline-none hover:no-underline
							transition-colors duration-200"
					>
						<div class="w-full {activeRoute === 'settings.edit' ? 'border-b-2 border-dotted border-blue-500' : ''} flex items-center">
							<Icon icon={icons.cog} class="size-4 mr-2"/>
							{t('misc.menu.settings')}
						</div>
					</a>

					<a
						use:inertia={{ method: 'post' }}
						href={route('logout')}
						class="lg:hidden px-3 py-1 text-gray-800
							hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-gray-100
							rounded uppercase
							border-b-2 border-solid border-transparent
							focus:outline-none hover:no-underline
							transition-colors duration-200
							flex items-center"
					>
						<Icon icon={icons.logout} class="size-5 mr-1 lg:hidden"/>
						<span>
							{t('misc.menu.logout')}<small class="ml-1 normal-case">({user.username})</small>
						</span>
					</a>

					<div class="hidden lg:block">
						<button
							onclick={(e) => {
								e.stopPropagation();
								dropdown = !dropdown;
							}}
							class="
								px-3 pt-6 pb-4 text-gray-800
								hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-cool-gray-100
								border-b-2 border-solid
								focus:outline-none hover:no-underline
								transition-color duration-200
								flex items-center
								{dropdown ? 'border-blue-500' : 'border-transparent hover:border-gray-400 focus:border-gray-400 active:border-blue-500'}
							"
							style:anchor-name="--account-dropdown-anchor"
						>
							{user.username}
							<svg
								xmlns="http://www.w3.org/2000/svg"
								viewBox="0 0 20 20"
								class="fill-current size-5 ml-1 transition-transform duration-150"
								class:rotate-180={!dropdown}
							>
								<path d="M10.707 7.05L10 6.343 4.343 12l1.414 1.414L10 9.172l4.243 4.242L15.657 12z"/>
							</svg>
						</button>

						{#if dropdown}
							<div
								bind:this={dropdownElement}
								class="flex absolute z-10 flex-col items-end"
								style:transform-origin="calc(100% - 2rem) top"
								transition:scale={{ duration: 150, start: 0.9 }}
								style:position-anchor="--account-dropdown-anchor"
								style:position-area="bottom center"
							>
								<div class="size-0 mr-8 z-20 border-8 border-t-0 border-r-transparent border-l-transparent border-b-white"></div>

								<div class="flex flex-col overflow-hidden bg-white rounded-lg shadow-2xl">
									<a
										use:inertia
										href={route('settings.edit')}
										class="
											pl-5 pr-12 py-4 text-gray-800
											hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-cool-gray-100
											border-l-2 border-solid
											{activeRoute === 'settings.edit' ? 'border-blue-500' : 'border-transparent hover:border-gray-400 focus:border-gray-400 active:border-blue-500'}
											focus:outline-none hover:no-underline
											transition-colors duration-200
										"
									>
										{t('misc.menu.settings')}
									</a>

									<a
										use:inertia={{ method: 'post' }}
										href={route('logout')}
										class="pl-5 pr-12 py-4 text-gray-800
											hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-cool-gray-100
											border-l-2 border-solid border-transparent
											hover:border-gray-400 focus:border-gray-400 active:border-blue-500
											focus:outline-none hover:no-underline
											transition-colors duration-200"
									>
										{t('misc.menu.logout')}
									</a>
								</div>
							</div>
						{/if}
					</div>
				{/if}

				<div class="lg:mt-1 px-2 py-1 text-gray-800 text-sm flex items-center">
					{t('misc.language')}:&nbsp;
					<div>
						{#each availableLocales as locale}
							{#if locale !== currentLocale}
								<Button
									inertia={{
										href: route('locale.store'),
										method: 'post',
										data: { language: locale },
									}}
									outline
									small
								>
									{locale.toUpperCase()}
								</Button>
								<span class="hidden"></span>
							{/if}
						{/each}
					</div>
				</div>
			</div>
		</div>
	</div>
</nav>

<style>
	.menu-padding {
		padding:
			env(safe-area-inset-top)
			max(env(safe-area-inset-left), 0.75rem)
			0
			max(env(safe-area-inset-right), 0.75rem);
	}
</style>
