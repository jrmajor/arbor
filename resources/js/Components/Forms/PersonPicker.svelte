<script lang="ts">
	import { onMount } from 'svelte';
	import { SvelteMap } from 'svelte/reactivity';
	import { route } from 'ziggy-js';
	import type { Sex } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import { formFieldContext } from './contexts';

	let {
		value = $bindable(),
		initialValue = null,
		sex = null,
		nullable = false,
		disabled = false,
	}: {
		value: number | null;
		initialValue: number | null;
		sex: Sex | null;
		nullable?: boolean;
		disabled?: boolean;
	} = $props();

	interface Person {
		id: number;
		name: string;
		dates: string | null;
	}

	// svelte-ignore non_reactive_update
	let searchInput: HTMLInputElement | null = null;

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

	const formField = formFieldContext.get();
</script>

<div class="w-full">
	<div class="relative w-full">
		<!-- svelte-ignore a11y_click_events_have_key_events -->
		<!-- svelte-ignore a11y_no_static_element_interactions -->
		<div
			onclick={() => searchInput?.focus()}
			class={{
				'dropdown-icon select': true,
				disabled,
				invalid: formField.error,
			}}
		>
			<div class="pr-4">
				<span>{value ? (names.get(value) ?? t('misc.loading')) : ''}</span><!--
				-->{#if !disabled}
					<input
						bind:this={searchInput}
						id={formField.id}
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
				{/if}
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
</div>

<style lang="postcss">
	.dropdown-icon {
		background-image: url(./dropdown.svg);
		background-position: right .5rem center;
		background-repeat: no-repeat;
		background-size: 1.5em 1.5em;
	}

	.select {
		cursor: text;
		@apply px-3 py-1.5 pr-9 rounded-md border border-gray-300;

		&:not(.disabled) {
			@apply focus:border-blue-600 focus:ring focus:ring-blue-500/50;
			@apply focus-within:border-blue-600 focus-within:ring focus-within:ring-blue-500/50;
			@apply active:border-blue-600 active:ring active:ring-blue-500/50;
		}
	}

	.invalid {
		@apply border-red-600;

		&:not(.disabled) {
			@apply focus:ring-red-500/50;
			@apply focus-within:ring-red-500/50;
			@apply active:ring-red-500/50;
		}
	}

	.disabled {
		cursor: default;
		@apply text-gray-700 bg-gray-100;
	}
</style>
