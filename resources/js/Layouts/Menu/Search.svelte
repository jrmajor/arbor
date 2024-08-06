<script lang="ts">
	import { route } from 'ziggy-js';
	import { t } from '@/helpers/translations';

	let { user }: { user: SharedUser | null } = $props();

	type Person = {
		id: number;
		name: string;
		dates: string | null;
		url: string;
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
		window.location.href = results[hoveredIndex].url;
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
</script>

<form
	role="search"
	class="relative mb-2 lg:mb-0 lg:mt-1 lg:mr-3 lg:w-96"
	onsubmit={(e) => e.preventDefault()}
>
	<input
		type="search"
		class="form-input w-full h-9"
		autocomplete="off"
		bind:value={search}
		{onkeydown}
		oninput={oninput}
		onfocus={() => isOpen = shouldCloseOnBlur = true}
		onblur={closeDropdown}
	>
	<div
		class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2
			text-gray-700 transition-colors duration-200 active:text-gray-900"
	>
		<svg class="fill-current size-5" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
			<path d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/>
		</svg>
	</div>
	{#if isOpen && search.length}
		<!-- svelte-ignore a11y_no_noninteractive_element_interactions -->
		<ul
			class="absolute mt-2 z-50 py-1 w-full text-gray-800 bg-white rounded-md shadow-md border border-gray-300"
			onmousedown={() => shouldCloseOnBlur = false}
		>
			{#each results as person, index (person.id)}
				<a href={route('people.show', person)} onclick={() => isOpen = false}>
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
