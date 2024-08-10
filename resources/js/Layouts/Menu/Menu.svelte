<script lang="ts">
	import { route, type RouteList } from 'ziggy-js';
	import { inertia } from '@inertiajs/svelte';
	import { t } from '@/helpers/translations';
	import Search from './Search.svelte';

	let { activeRoute, user }: {
		activeRoute: keyof RouteList;
		user: SharedUser | null;
	} = $props();

	let containerElement: HTMLElement;
	// svelte-ignore non_reactive_update
	let dropdownElement: HTMLElement;

	let open = $state(false);
	let dropdown = $state(false);

	function clickOutside(event: MouseEvent) {
		if (open && !containerElement.contains(event.target as Node)) open = false;
		if (dropdown && !dropdownElement.contains(event.target as Node)) dropdown = false;
	}
</script>

<svelte:document onclick={clickOutside}/>

<nav bind:this={containerElement} class="mb-1 bg-white shadow-md">
	<div class="container mx-auto">
		<div class="px-3 flex items-center justify-between flex-wrap">
			<div class="flex items-center">
				<a
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
					<svg class="fill-current size-4 mr-2" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
						<path d="M460.8 64V38.4H345.6v64H140.8v140.8H51.2v25.6h89.6v140.8h204.8v64h115.2V448h-89.6V345.6h89.6V320H345.6v64H166.4V128h179.2v64h115.2v-25.6h-89.6V64z"/>
						<path d="M0 179.8h102.4v152.4H0zM409.6 128H512v102.4H409.6zM409.6 0H512v102.4H409.6zM204.8 64h102.4v102.4H204.8zM204.8 345.6h102.4V448H204.8zM409.6 409.6H512V512H409.6zM409.6 281.6H512V384H409.6z"/>
					</svg>
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
				class="flex-col pb-2 mt-2 w-full lg:!flex lg:flex-row lg:w-auto lg:mt-0 lg:pb-0 lg:items-center"
				class:flex={open}
				class:hidden={!open}
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
							<svg class="fill-current size-4 mr-2 lg:hidden" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
								<path d="M2 6H0v2h2v2h2V8h2V6H4V4H2v2zm7 0a3 3 0 0 1 6 0v2a3 3 0 0 1-6 0V6zm11 9.14A15.93 15.93 0 0 0 12 13c-2.91 0-5.65.78-8 2.14V18h16v-2.86z"/>
							</svg>
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
							<svg class="fill-current size-4 mr-2 lg:hidden" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
								<path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm-5.6-4.29a9.95 9.95 0 0 1 11.2 0 8 8 0 1 0-11.2 0zm6.12-7.64l3.02-3.02 1.41 1.41-3.02 3.02a2 2 0 1 1-1.41-1.41z"/>
							</svg>
							{t('misc.menu.dashboard')}
						</div>
					</a>
				{/if}

				{#if !user}
					<a
						href="mailto:jeremiah.major@npng.pl"
						class="px-3 py-1 lg:pt-6 lg:pb-4 text-gray-800
							hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 lg:hover:bg-gray-100 lg:focus:bg-cool-gray-100
							rounded lg:rounded-none uppercase lg:normal-case
							border-b-2 border-solid border-transparent
							lg:hover:border-gray-400 lg:focus:border-gray-400 lg:active:border-blue-500
							focus:outline-none hover:no-underline
							transition-colors duration-200
							flex items-center"
					>
						<svg class="fill-current size-4 mr-2 lg:hidden" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
							<path d="M13.6 13.47A4.99 4.99 0 0 1 5 10a5 5 0 0 1 8-4V5h2v6.5a1.5 1.5 0 0 0 3 0V10a8 8 0 1 0-4.42 7.16l.9 1.79A10 10 0 1 1 20 10h-.18.17v1.5a3.5 3.5 0 0 1-6.4 1.97zM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
						</svg>
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
						<svg class="stroke-current size-5 -ml-1 mr-1 lg:hidden" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
							<path d="M192 176v-40a40 40 0 0140-40h160a40 40 0 0140 40v240a40 40 0 01-40 40H240c-22.09 0-48-17.91-48-40v-40" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="38"/>
							<path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="38" d="M288 336l80-80-80-80M80 256h272"/>
						</svg>
						{t('misc.menu.login')}
					</a>
				{:else}
					<a
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
							<svg class="fill-current size-4 mr-2" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
								<path d="M3.94 6.5L2.22 3.64l1.42-1.42L6.5 3.94c.52-.3 1.1-.54 1.7-.7L9 0h2l.8 3.24c.6.16 1.18.4 1.7.7l2.86-1.72 1.42 1.42-1.72 2.86c.3.52.54 1.1.7 1.7L20 9v2l-3.24.8c-.16.6-.4 1.18-.7 1.7l1.72 2.86-1.42 1.42-2.86-1.72c-.52.3-1.1.54-1.7.7L11 20H9l-.8-3.24c-.6-.16-1.18-.4-1.7-.7l-2.86 1.72-1.42-1.42 1.72-2.86c-.3-.52-.54-1.1-.7-1.7L0 11V9l3.24-.8c.16-.6.4-1.18.7-1.7zM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
							</svg>
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
						<svg class="stroke-current size-5 mr-1 lg:hidden" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
							<path d="M304 336v40a40 40 0 01-40 40H104a40 40 0 01-40-40V136a40 40 0 0140-40h152c22.09 0 48 17.91 48 40v40M368 336l80-80-80-80M176 256h256" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="38"/>
						</svg>
						<span>
							{t('misc.menu.logout')}<small class="ml-1 normal-case">({user.username})</small>
						</span>
					</a>

					<div class="hidden lg:block relative">
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
						>
							{user.username}
							<svg class="fill-current size-5 ml-1" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
								{#if dropdown}
									<path d="M10.707 7.05L10 6.343 4.343 12l1.414 1.414L10 9.172l4.243 4.242L15.657 12z"/>
								{:else}
									<path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
								{/if}
							</svg>
						</button>

						{#if dropdown}
							<div
								bind:this={dropdownElement}
								class="flex absolute right-0 z-10 flex-col items-end"
							>
								<div class="size-0 mr-8 z-20 border-8 border-t-0 border-r-transparent border-l-transparent border-b-white"></div>

								<div class="flex flex-col overflow-hidden bg-white rounded-lg shadow-2xl">
									<a
										use:inertia
										href={route('settings.edit')}
										class="pl-5 pr-12 py-4 text-gray-800
											hover:text-gray-900 focus:text-gray-900 hover:bg-gray-100 focus:bg-cool-gray-100
											border-l-2 border-solid
											{activeRoute === 'settings.edit' ? 'border-blue-500' : 'border-transparent hover:border-gray-400 focus:border-gray-400 active:border-blue-500'}
											focus:outline-none hover:no-underline
											transition-colors duration-200"
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
						{#each globalThis.arborProps.otherAvailableLocales as locale}
							{#if locale !== globalThis.arborProps.currentLocale}
								<button
									use:inertia={{
										href: route('locale.store'),
										method: 'post',
										data: { language: locale },
										onFinish: () => location.reload(),
									}}
									class="btn-out leading-none text-xs rounded px-2"
								>
									{locale.toUpperCase()}
								</button>
								<span class="hidden"></span>
							{/if}
						{/each}
					</div>
				</div>
			</div>
		</div>
	</div>
</nav>
