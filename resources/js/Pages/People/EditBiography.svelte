<script lang="ts" module>
	export { default as layout } from './Components/Layout/Layout.svelte';
</script>

<script lang="ts">
	import { useForm } from '@inertiajs/svelte';
	import { route } from 'ziggy-js';
	import type { PersonPage } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import * as Form from '@/Components/Forms';
	import Button from '@/Components/Primitives/Button.svelte';

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

<form {onsubmit} class="p-6 bg-white rounded-lg shadow-sm overflow-hidden">
	<Form.Field error={$form.errors.biography}>
		<Form.Textarea bind:value={$form.biography} rows={20}/>
	</Form.Field>

	<div class="-m-6 mt-6 px-6 py-4 bg-gray-50 flex justify-end">
		<Button type="submit">{t('misc.save')}</Button>
	</div>
</form>
