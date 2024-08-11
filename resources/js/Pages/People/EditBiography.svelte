<script lang="ts" context="module">
	export { default as layout } from './Components/Layout/Layout.svelte';
</script>

<script lang="ts">
	import { route } from 'ziggy-js';
	import { useForm } from '@inertiajs/svelte';
	import type { PersonPage } from '@/types/resources/people';
	import { t } from '@/helpers/translations';

	let { person }: {
		person: { biography: string | null } & PersonPage;
	} & SharedProps = $props();

	const form = useForm({ biography: person.biography });

	function onsubmit(event: SubmitEvent) {
		event.preventDefault();

		$form.patch(route('people.biography.update', person));
	}
</script>

<svelte:head>
	<title>{person.simpleName} - {t('people.titles.biography_editing')} - Arbor</title>
</svelte:head>

<form {onsubmit} class="p-6 bg-white rounded-lg shadow overflow-hidden">
	<div>
		<fieldset class="w-full flex flex-col">
			<!-- svelte-ignore a11y_autofocus -->
			<textarea
				autofocus
				id="biography"
				rows="20"
				bind:value={$form.biography}
				class="form-input w-full min-h-full resize-y"
				class:invalid={$form.errors.biography}
			></textarea>
			{#if $form.errors.biography}
				<div class="w-full leading-none mt-1">
					<small class="text-red-500">{$form.errors.biography}</small>
				</div>
			{/if}
		</fieldset>

		<div class="-m-6 mt-6 px-6 py-4 bg-gray-50 flex justify-end">
			<button type="submit" class="btn">
				{t('misc.save')}
			</button>
		</div>
	</div>
</form>
