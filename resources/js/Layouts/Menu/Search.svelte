<script lang="ts">
	import { onMount } from 'svelte';
	import { router } from '@inertiajs/svelte';
	import { route } from 'ziggy-js';
	import { hotkey } from '@/helpers/hotkey';
	import { inertia } from '@/helpers/inertia';
	import { t } from '@/helpers/translations';
	import Icon, { icons } from '@/Components/Icons/Icon.svelte';

	let { user, class: className = '' }: {
		user: SharedUser | null;
		class?: string;
	} = $props();

	type Person = {
		id: number;
		name: string;
		dates: string | null;
	};

	let search = $state('');
	let previousSearch = '';
	let results: Person[] = $state([]);
	let moreCount = $state(0);
	let hiddenCount = $state(0);

	let isOpen = $state(false);
	let shouldCloseOnBlur = $state(true);
	let hoveredIndex: number | null = $state(null);

	function oninput() {
		isOpen = true;

		if (search.length < 1) {
			results = [];
			return;
		}

		if (search === previousSearch) return;

		fetch(route('people.search', { search }))
			.then((response) => response.json())
			.then((data) => {
				results = data.people;
				moreCount = data.moreCount;
				hiddenCount = data.hiddenCount;

				if (hoveredIndex && hoveredIndex > results.length - 1) hoveredIndex = null;
			});

		previousSearch = search;
	}

	function onkeydown(event: KeyboardEvent) {
		const listener = {
			ArrowUp: () => arrow('up'),
			ArrowDown: () => arrow('down'),
			Enter: enter,
			Escape: closeDropdown,
		}[event.key];

		if (!listener) return;
		event.preventDefault();
		listener();
	}

	function arrow(direction: 'up' | 'down') {
		if (results.length === 0) return;

		if (hoveredIndex === null) {
			hoveredIndex = direction === 'up' ? results.length - 1 : 0;
			return;
		}

		hoveredIndex = direction === 'up' ? hoveredIndex - 1 : hoveredIndex + 1;

		if (hoveredIndex < 0) hoveredIndex = results.length - 1;
		if (hoveredIndex > results.length - 1) hoveredIndex = 0;
	}

	function enter() {
		if (hoveredIndex === null) return;
		isOpen = false;
		router.get(route('people.show', results[hoveredIndex]));
	}

	function closeDropdown() {
		if (!shouldCloseOnBlur) {
			shouldCloseOnBlur = true;
			return;
		}

		isOpen = false;
		hoveredIndex = null;
		shouldCloseOnBlur = true;
	}

	onMount(() => {
		router.on('start', closeDropdown);
		router.on('finish', () => {
			search = '';
			previousSearch = '';
			results = [];
			moreCount = 0;
			hiddenCount = 0;
		});
	});
</script>

<form
	role="search"
	class={['relative', className]}
	onsubmit={(e) => e.preventDefault()}
	style:anchor-name="--search-bar-anchor"
>
	<input
		{@attach hotkey('s,/')}
		type="search"
		class="form-input w-full h-9"
		autocomplete="off"
		bind:value={search}
		{onkeydown}
		{oninput}
		onfocus={() => isOpen = shouldCloseOnBlur = true}
		onblur={closeDropdown}
	>
	<div
		class="
			pointer-events-none absolute inset-y-0 right-0 flex items-center px-2
			text-gray-700 transition-colors duration-200 active:text-gray-900
		"
	>
		<Icon icon={icons.search} class="size-5"/>
	</div>
	{#if isOpen && search.length}
		<!-- svelte-ignore a11y_no_noninteractive_element_interactions -->
		<ul
			class="absolute my-2 z-60 py-1 w-full text-gray-800 bg-white rounded-md shadow-md border border-gray-300"
			style:position-anchor="--search-bar-anchor"
			style:position-area="bottom center"
			onmousedown={() => shouldCloseOnBlur = false}
		>
			{#each results as person, index (person.id)}
				<a
					{@attach inertia()}
					href={route('people.show', person)}
					onclick={() => isOpen = false}
				>
					<!-- svelte-ignore a11y_mouse_events_have_key_events -->
					<li
						onmouseover={() => hoveredIndex = index}
						class="select-none w-full px-3 py-1 text-gray-800 flex justify-between items-center"
						class:bg-cool-gray-100={hoveredIndex === index}
					>
						<span>
							{person.name}
							{#if person.dates}
								<small>({person.dates})</small>
							{/if}
						</span>
						{#if user?.isSuperAdmin}
							<small class="tabular-nums">№{person.id}</small>
						{/if}
					</li>
				</a>
			{:else}
				<li class="w-full px-3 py-1 text-gray-600">
					{t('misc.no_results')}
				</li>
			{/each}
			{#if moreCount || hiddenCount}
				<li class="select-none w-full px-3 py-1 text-gray-800 flex justify-between items-center">
					<small>
						+
						{#if moreCount}
							<b>{moreCount}</b> more
						{/if}
						{#if moreCount && hiddenCount}
							and
						{/if}
						{#if hiddenCount}
							<b>{hiddenCount}</b> hidden
						{/if}
					</small>
				</li>
			{/if}
		</ul>
	{/if}
</form>
