<script lang="ts">
	import { SvelteMap } from 'svelte/reactivity';
	import { route } from 'ziggy-js';
	import type { Sex } from '@/types/people';
	import { t } from '@/helpers/translations';
	import { onMount } from 'svelte';

	let {
		label,
		value = $bindable(),
		initialValue = null,
		sex = null,
		nullable = false,
		error,
		class: className = '',
	}: {
		label: string;
		value: number | null;
		initialValue: number | null;
		sex: Sex | null;
		nullable?: boolean;
		error: string | null;
		class?: string;
	} = $props();

	const id = `person${Math.floor(Math.random() * (10 ** 8))}`;

	interface Person {
		id: number;
		name: string;
		dates: string | null;
	}

	let searchInput: HTMLInputElement;

	let searchValue = $state('');
	let previousSearchValue = '';
	let results: Person[] = $state([]);

	let names = new SvelteMap<number, string>();

	let isOpen = $state(false);
	let shouldCloseOnBlur = $state(true);
	let hoveredIndex: number | null = $state(null);

	onMount(() => {
		if (value !== null) fetchPeople(value.toString());
	});

	function oninput(event: Event) {
		if (value !== null) {
			event.preventDefault();
			return;
		}

		if (searchValue !== previousSearchValue) {
			fetchPeople();
			previousSearchValue = searchValue;
		}
	}

	function fetchPeople(query: string = searchValue) {
		fetch(route('people.search', { sex, search: query }))
			.then((response) => response.json())
			.then((data) => {
				results = data.people;
				if ((hoveredIndex ?? 0) > results.length - 1) hoveredIndex = null;
				for (const person of data.people) {
					names.set(person.id, person.name);
				}
			});
	}

	function select(person: Person) {
		value = person.id;
		searchValue = '';
		isOpen = false;
	}

	function deselect() {
		value = null;
		searchValue = '';
		isOpen = true;
	}

	function onkeydown(event: KeyboardEvent) {
		if (event.key === 'Backspace') {
			if (!value) return;
			event.preventDefault();
			deselect();
		}

		const listener = {
			ArrowUp: () => arrow('up'),
			ArrowDown: () => arrow('down'),
			Enter: enter,
			Escape: closeDropdown,
		}[event.key];

		if (listener) {
			event.preventDefault();
			listener();
		}

		// don't allow input when some value is selected
		if (value === null) return;
		if (event.key === 'Tab' || event.metaKey || event.ctrlKey) return;
		event.preventDefault();
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
		if (hoveredIndex !== null) select(results[hoveredIndex]);
	}

	function closeDropdown() {
		if (!shouldCloseOnBlur) {
			shouldCloseOnBlur = true;
			return;
		}

		isOpen = false;
		if (!nullable && value === null && initialValue !== null) {
			value = initialValue;
			searchValue = '';
		}

		hoveredIndex = null;
		shouldCloseOnBlur = true;
	}
</script>

<div class="flex flex-col {className}">
	<label for={id} class="w-full font-medium pb-1 text-gray-700">{label}</label>
	<div class="w-full">
		<div class="relative w-full">
			<!-- svelte-ignore a11y_click_events_have_key_events -->
			<!-- svelte-ignore a11y_no_static_element_interactions -->
			<div
				onclick={() => searchInput.focus()}
				class="dropdown-icon cursor-text form-select"
				class:invalid={error}
			>
				<div class="pr-4">
					<span>{value ? (names.get(value) ?? t('misc.loading')) : ''}</span><!--
				--><input
						bind:this={searchInput}
						{id}
						type="text"
						bind:value={searchValue}
						autocomplete="off"
						{onkeydown}
						{oninput}
						onfocus={() => isOpen = shouldCloseOnBlur = true}
						onblur={closeDropdown}
						class="p-0 outline-none border-0 focus:ring-0"
						style:width={value ? '4px' : '100%'}
						style:margin-left={value ? '1px' : '0'}
					>
				</div>
			</div>
			{#if isOpen && searchValue}
				<!-- svelte-ignore a11y_no_noninteractive_element_interactions -->
				<ul
					class="absolute mt-2 z-50 py-1 w-full text-gray-800 bg-white rounded-md shadow-md border border-gray-300"
					onmousedown={() => shouldCloseOnBlur = false}
				>
					{#each results as person, index (person.id)}
						<!-- svelte-ignore a11y_click_events_have_key_events -->
						<!-- svelte-ignore a11y_mouse_events_have_key_events -->
						<li
							onmouseover={() => hoveredIndex = index}
							onclick={() => select(person)}
							class="select-none w-full px-3 py-1 text-gray-800 flex justify-between items-center"
							class:bg-cool-gray-100={hoveredIndex === index}
						>
							<span>
								<span>{person.name}</span>
								<small>[№{person.id}]</small>
							</span>
							<span class="text-gray-800">{value === person.id ? '✓ ' : ''}</span>
						</li>
					{:else}
						<li class="w-full px-3 py-1 text-gray-600">
							{t('misc.no_results')}
						</li>
					{/each}
				</ul>
			{/if}
		</div>
		{#if error}
			<small class="text-red-500">{error}</small>
		{/if}
	</div>
</div>

<style lang="postcss">
	.dropdown-icon {
		background-image: url(&quot;data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E&quot;);
		background-position: right .5rem center;
		background-repeat: no-repeat;
		background-size: 1.5em 1.5em;
	}
</style>
