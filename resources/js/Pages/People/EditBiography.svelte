<script lang="ts">
	import { route } from 'ziggy-js';
	import { useForm } from '@inertiajs/svelte';
	import type { PersonPage } from '@/types/people';
	import { t } from '@/helpers/translations';
	import TitleBar from './Components/TitleBar.svelte';
	import SidebarMenu from './Components/SidebarMenu.svelte';

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

<h1 class="mb-3 leading-none text-3xl font-medium">
	<TitleBar {person}/>
</h1>

<div class="flex flex-col md:flex-row space-x-2 space-y-2">
	<main class="grow md:w-1/2 p-6 bg-white rounded-lg shadow overflow-hidden">
		<form {onsubmit}>
			<div>
				<fieldset>
					<!-- https://bugs.chromium.org/p/chromium/issues/detail?id=375693 -->
					<div class="w-full flex flex-col">
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
					</div>
				</fieldset>

				<div class="-m-6 mt-6 px-6 py-4 bg-gray-50 flex justify-end">
					<button type="submit" class="btn">
						{t('misc.save')}
					</button>
				</div>
			</div>
		</form>
	</main>

	<div class="shrink-0 p-1">
		<SidebarMenu {person} activePage="biography"/>
	</div>
</div>
