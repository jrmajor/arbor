<script lang="ts">
	import { onMount } from 'svelte';
	import { route } from 'ziggy-js';
	import { t } from '@/helpers/translations';
	import * as Form from '@/Components/Forms';
	import Link from '@/Components/Primitives/Link.svelte';

	let { value = $bindable(), error }: {
		value: string | null;
		error: string | null;
	} = $props();

	let result: string | null = $state(null);

	function oninput() {
		value = value?.trim() ?? '';

		if (!value) {
			result = '←';
			return;
		}

		const intValue = parseInt(value);
		if (String(intValue) !== value) {
			result = null;
			return;
		}

		fetch(route('ajax.pytlewskiName', { id: intValue }))
			.then((response) => response.json())
			.then((data) => result = data.result);
	}

	onMount(() => oninput());
</script>

<div class="flex flex-col">
	<label for="id_pytlewski" class="w-full font-medium pb-1 text-gray-700">
		{t('people.id_in')}
		<Link href="http://www.pytlewski.pl/index/drzewo/" external>pytlewski.pl</Link>
	</label>
	<div class="w-full flex">
		<input
			type="text"
			inputmode="numeric"
			bind:value
			{oninput}
			id="id_pytlewski"
			autocomplete="off"
			class="form-input rounded-r-none w-1/4 md:w-3/12 z-10"
			class:invalid={error}
		>
		<input
			type="text"
			class="form-input rounded-l-none -ml-px w-3/4 md:w-9/12"
			value={result ?? t('people.pytlewski.not_found')}
			disabled
		>
	</div>
	<Form.Error error={error}/>
</div>
