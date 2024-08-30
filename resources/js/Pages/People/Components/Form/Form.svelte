<script lang="ts">
	import { slide } from 'svelte/transition';
	import { route } from 'ziggy-js';
	import { useForm } from '@inertiajs/svelte';
	import { Sex, type EditPersonResource } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import DateRangePicker from '@/Components/DateRangePicker.svelte';
	import PersonPicker from '@/Components/PersonPicker.svelte';
	import PytlewskiPicker from './PytlewskiPicker.svelte';

	let { person, action }: {
		person: EditPersonResource;
		action: 'create' | 'edit';
	} = $props();

	const form = useForm({
		sex: person.sex,
		name: person.name,
		middle_name: person.middleName,
		family_name: person.familyName,
		last_name: person.lastName,
		id_wielcy: person.wielcyId,
		id_pytlewski: person.pytlewskiId ? String(person.pytlewskiId) : null,
		birth_date_from: person.birthDateFrom,
		birth_date_to: person.birthDateTo,
		birth_place: person.birthPlace,
		dead: person.isDead,
		death_date_from: person.deathDateFrom,
		death_date_to: person.deathDateTo,
		death_place: person.deathPlace,
		death_cause: person.deathCause,
		funeral_date_from: person.funeralDateFrom,
		funeral_date_to: person.funeralDateTo,
		funeral_place: person.funeralPlace,
		burial_date_from: person.burialDateFrom,
		burial_date_to: person.burialDateTo,
		burial_place: person.burialPlace,
		father_id: person.fatherId,
		mother_id: person.motherId,
		sources: person.sources,
	});

	let sourceError = $derived.by(() => {
		if ($form.errors.sources) return $form.errors.sources;
		for (let i = 0; i < $form.sources.length; i++) {
			const key = `sources.${i}`;
			// @ts-expect-error $form.errors thinks keys can only be form keys
			if ($form.errors[key]) return $form.errors[key];
		}
		return null;
	});

	function onsubmit(event: SubmitEvent) {
		event.preventDefault();

		$form.transform((data) => ({
			_method: action === 'create' ? 'post' : 'put',
			...data,
		})).post(
			action === 'create'
				? route('people.store')
				: route('people.update', person),
		);
	}
</script>

<form {onsubmit} class="p-6 bg-white rounded-lg shadow overflow-hidden">
	<div>
		<fieldset class="space-y-5">
			<div class="flex flex-col">
				<legend class="w-full font-medium pb-1 text-gray-700">{t('people.sex')}</legend>
				<div class="w-full">
					<div class="flex flex-col sm:flex-row sm:space-x-6">
						<div class="w-full sm:w-auto flex items-center">
							<input
								type="radio"
								id="sex_male"
								bind:group={$form.sex}
								value="xy"
								class="form-radio"
							>
							<label class="ml-2" for="sex_male">{t('people.male')}</label>
						</div>
						<div class="w-full sm:w-auto flex items-center">
							<input
								type="radio"
								id="sex_female"
								bind:group={$form.sex}
								value="xx"
								class="form-radio"
							>
							<label class="ml-2" for="sex_female">{t('people.female')}</label>
						</div>
						<div class="w-full sm:w-auto flex items-center">
							<input
								type="radio"
								id="sex_unknown"
								bind:group={$form.sex}
								value={null}
								class="form-radio"
							>
							<label class="ml-2" for="sex_unknown">{t('people.unknown')}</label>
						</div>
					</div>
				</div>
			</div>

			<div class="space-y-5 lg:space-y-0 lg:space-x-5 flex flex-col lg:flex-row">
				<div class="space-y-5 sm:space-y-0 sm:space-x-5 w-full lg:w-1/2 flex flex-col sm:flex-row">
					<div class="w-full sm:w-1/2 flex flex-col">
						<label for="name" class="w-full font-medium pb-1 text-gray-700">{t('people.name')}</label>
						<div class="w-full">
							<input
								type="text"
								id="name"
								bind:value={$form.name}
								class="form-input w-full"
								class:invalid={$form.errors.name}
							>
							{#if $form.errors.name}
								<div class="w-full leading-none mt-1">
									<small class="text-red-500">{$form.errors.name}</small>
								</div>
							{/if}
						</div>
					</div>
					<div class="w-full sm:w-1/2 flex flex-col">
						<label for="middle_name" class="w-full font-medium pb-1 text-gray-700">{t('people.middle_name')}</label>
						<div class="w-full">
							<input
								type="text"
								id="middle_name"
								bind:value={$form.middle_name}
								class="form-input w-full"
								class:invalid={$form.errors.middle_name}
							>
							{#if $form.errors.middle_name}
								<div class="w-full leading-none mt-1">
									<small class="text-red-500">{$form.errors.middle_name}</small>
								</div>
							{/if}
						</div>
					</div>
				</div>
				<div class="space-y-5 sm:space-y-0 sm:space-x-5 w-full lg:w-1/2 flex flex-col sm:flex-row">
					<div class="w-full sm:w-1/2 flex flex-col">
						<label for="family_name" class="w-full font-medium pb-1 text-gray-700">{t('people.family_name')}</label>
						<div class="w-full">
							<input
								type="text"
								id="family_name"
								bind:value={$form.family_name}
								class="form-input w-full"
								class:invalid={$form.errors.family_name}
							>
							{#if $form.errors.family_name}
								<div class="w-full leading-none mt-1">
									<small class="text-red-500">{$form.errors.family_name}</small>
								</div>
							{/if}
						</div>
					</div>
					<div class="w-full sm:w-1/2 flex flex-col">
						<label for="last_name" class="w-full font-medium pb-1 text-gray-700">{t('people.last_name')}</label>
						<div class="w-full">
							<input
								type="text"
								id="last_name"
								bind:value={$form.last_name}
								class="form-input w-full"
								class:invalid={$form.errors.last_name}
							>
							{#if $form.errors.last_name}
								<div class="w-full leading-none mt-1">
									<small class="text-red-500">{$form.errors.last_name}</small>
								</div>
							{/if}
						</div>
					</div>
				</div>
			</div>
		</fieldset>

		<hr class="mt-7 mb-6 text-gray-200">

		<div class="w-full mb-4">
			<div class="font-medium text-xl text-gray-900">{t('people.external_links')}</div>
		</div>
		<fieldset class="space-y-5 md:space-y-0 md:space-x-5 flex flex-col md:flex-row">
			<div class="w-full md:w-1/2 flex flex-col">
				<label for="id_wielcy" class="w-full font-medium pb-1 text-gray-700">{@html t('people.wielcy.id')}</label>
				<div class="w-full flex">
					<input
						type="text"
						id="id_wielcy"
						bind:value={$form.id_wielcy}
						class="form-input rounded-r-none w-1/4 md:w-3/8 z-10"
						class:invalid={$form.errors.id_wielcy}
					>
					<input
						type="text"
						id="wielcy_search"
						placeholder={t('misc.coming_soon')}
						disabled
						class="form-input rounded-l-none -ml-px w-3/4 md:w-5/8"
					>
				</div>
				{#if $form.errors.id_wielcy}
					<div class="w-full leading-none mt-1">
						<small class="text-red-500">{$form.errors.id_wielcy}</small>
					</div>
				{/if}
			</div>
			<div class="w-full md:w-1/2">
				<PytlewskiPicker bind:value={$form.id_pytlewski} error={$form.errors.id_pytlewski ?? null}/>
			</div>
		</fieldset>

		<hr class="mt-7 mb-6 text-gray-200">

		<!--
		<div class="flex flex-wrap mb-2">
			<label class="w-full font-medium pb-1 text-gray-700">{t('people.pytlewski.guess')}</label>
			<div class="w-full flex flex-wrap">
				<div class="w-1/3">
					<button
						type="button" class="w-full"
						id="pytlewski_names"
						onclick={pytlewskiNames}
						disabled={!$form.id_pytlewski}
					>{t('people.pytlewski.names')}</button>
				</div>
				<div class="w-1/3 px-2">
					<button
						type="button" class="w-full"
						id="pytlewski_birth"
						onclick={pytlewskiBirth}
						disabled={!$form.id_pytlewski}
					>{t('people.pytlewski.birth')}</button>
				</div>
				<div class="w-1/3">
					<button
						type="button" class="w-full"
						id="pytlewski_death"
						onclick={pytlewskiDeath}
						disabled={!$form.id_pytlewski}
					>{t('people.pytlewski.death')}</button>
				</div>
			</div>
		</div>
		-->

		<div class="w-full mb-4">
			<div class="font-medium text-xl text-gray-900">{t('people.birth')}</div>
		</div>
		<fieldset class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
			<div class="w-full sm:w-1/2 flex flex-col">
				<label for="birth_place" class="w-full font-medium pb-1 text-gray-700">{t('misc.place')}</label>
				<div class="w-full">
					<input
						type="text"
						id="birth_place"
						bind:value={$form.birth_place}
						class="form-input w-full"
						class:invalid={$form.errors.birth_place}
					>
					{#if $form.errors.birth_place}
						<div class="w-full leading-none mt-1">
							<small class="text-red-500">{$form.errors.birth_place}</small>
						</div>
					{/if}
				</div>
			</div>
			<DateRangePicker
				label={t('misc.date.date')}
				bind:from={$form.birth_date_from}
				bind:to={$form.birth_date_to}
				errorFrom={$form.errors.birth_date_from ?? null}
				errorTo={$form.errors.birth_date_to ?? null}
				class="w-full sm:w-1/2"
			/>
		</fieldset>

		<hr class="mt-7 mb-6 text-gray-200">

		<div class="w-full flex items-center mb-4">
			<label for="dead" class="font-medium text-xl text-gray-900">
				{$form.sex === 'xx' ? t('people.dead.xx') : t('people.dead.xy')}
			</label>
			<input type="hidden" id="dead-hidden" name="dead" value="0">
			<input
				type="checkbox"
				id="dead"
				bind:checked={$form.dead}
				class="form-checkbox ml-2 size-4"
			>
		</div>

		{#if $form.dead}
			<fieldset class="space-y-5" transition:slide>
				<div class="w-full">
					<label for="death_cause" class="w-full font-medium pb-1 text-gray-700">{t('people.death_cause')}</label>
					<div class="w-full">
						<input
							type="text"
							id="death_cause"
							bind:value={$form.death_cause}
							class="form-input w-full sm:w-2/3 lg:w-1/3"
							class:invalid={$form.errors.death_cause}
						>
						{#if $form.errors.death_cause}
							<div class="w-full leading-none mt-1">
								<small class="text-red-500">{$form.errors.death_cause}</small>
							</div>
						{/if}
					</div>
				</div>

				<div class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
					<div class="w-full sm:w-1/2 flex flex-col">
						<label for="death_place" class="w-full font-medium pb-1 text-gray-700">{t('people.death_place')}</label>
						<div class="w-full">
							<input
								type="text"
								id="death_place"
								bind:value={$form.death_place}
								class="form-input w-full"
								class:invalid={$form.errors.death_place}
							>
							{#if $form.errors.death_place}
								<div class="w-full leading-none mt-1">
									<small class="text-red-500">{$form.errors.death_place}</small>
								</div>
							{/if}
						</div>
					</div>
					<DateRangePicker
						label={t('people.death_date')}
						bind:from={$form.death_date_from}
						bind:to={$form.death_date_to}
						errorFrom={$form.errors.death_date_from ?? null}
						errorTo={$form.errors.death_date_to ?? null}
						class="w-full sm:w-1/2"
					/>
				</div>

				<div class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
					<div class="w-full sm:w-1/2 flex flex-col">
						<label for="funeral_place" class="w-full font-medium pb-1 text-gray-700">{t('people.funeral_place')}</label>
						<div class="w-full">
							<input
								type="text"
								id="funeral_place"
								bind:value={$form.funeral_place}
								class="form-input w-full"
								class:invalid={$form.errors.funeral_place}
							>
							{#if $form.errors.funeral_place}
								<div class="w-full leading-none mt-1">
									<small class="text-red-500">{$form.errors.funeral_place}</small>
								</div>
							{/if}
						</div>
					</div>
					<DateRangePicker
						label={t('people.funeral_date')}
						bind:from={$form.funeral_date_from}
						bind:to={$form.funeral_date_to}
						errorFrom={$form.errors.funeral_date_from ?? null}
						errorTo={$form.errors.funeral_date_to ?? null}
						class="w-full sm:w-1/2"
					/>
				</div>

				<div class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
					<div class="w-full sm:w-1/2 flex flex-col">
						<label for="burial_place" class="w-full font-medium pb-1 text-gray-700">{t('people.burial_place')}</label>
						<div class="w-full">
							<input
								type="text"
								id="burial_place"
								bind:value={$form.burial_place}
								class="form-input w-full"
								class:invalid={$form.errors.burial_place}
							>
							{#if $form.errors.burial_place}
								<div class="w-full leading-none mt-1">
									<small class="text-red-500">{$form.errors.burial_place}</small>
								</div>
							{/if}
						</div>
					</div>
					<DateRangePicker
						label={t('people.burial_date')}
						bind:from={$form.burial_date_from}
						bind:to={$form.burial_date_to}
						errorFrom={$form.errors.burial_date_from ?? null}
						errorTo={$form.errors.burial_date_to ?? null}
						class="w-full sm:w-1/2"
					/>
				</div>
			</fieldset>
		{/if}

		<hr class="mt-7 mb-6 text-gray-200">

		<div class="w-full mb-4">
			<div class="font-medium text-xl text-gray-900">{t('people.parents')}</div>
		</div>
		<fieldset class="space-y-5">
			<div class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
				<PersonPicker
					label={t('people.mother')}
					bind:value={$form.mother_id}
					initialValue={person.motherId}
					sex={Sex.FEMALE}
					nullable
					error={$form.errors.mother_id ?? null}
					class="w-full sm:w-1/2"
				/>
				<PersonPicker
					label={t('people.father')}
					bind:value={$form.father_id}
					initialValue={person.fatherId}
					sex={Sex.MALE}
					nullable
					error={$form.errors.father_id ?? null}
					class="w-full sm:w-1/2"
				/>
			</div>
		</fieldset>

		<hr class="mt-7 mb-6 text-gray-200">

		<div class="w-full flex items-center justify-between mb-4">
			<div class="font-medium text-xl text-gray-900">{t('people.sources')}</div>
			<button
				type="button"
				onclick={() => $form.sources = [...$form.sources, '']}
				class="size-6 rounded-full border border-blue-700 p-1 text-blue-700 transition
					hover:bg-blue-100 hover:text-blue-800
					focus:outline-none focus:ring
					active:border-blue-600 active:bg-blue-600 active:text-blue-100"
			>
				<svg class="fill-current" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M8 6V2.5H6V6H2.5v2H6v3.5h2V8h3.5V6H8z"/>
				</svg>
			</button>
		</div>
		<fieldset class="space-y-5">
			<div class="w-full">
				{#if $form.sources.length}
					<div class="space-y-2">
						<!-- eslint-disable-next-line @typescript-eslint/no-unused-vars -->
						{#each $form.sources as _, index (index)}
							<div class="w-full flex items-center space-x-2">
								<input type="text" class="form-input w-full" bind:value={$form.sources[index]}>
								<div>
									<button
										type="button"
										onclick={() => $form.sources = [...$form.sources.slice(0, index), ...$form.sources.slice(index + 1)]}
										class="size-6 p-1 rounded-full border border-blue-700 text-blue-700 transition
											hover:bg-blue-100 hover:text-blue-800
											focus:outline-none focus:ring
											active:bg-blue-600 active:border-blue-600 active:text-blue-100"
									>
										<svg class="fill-current" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
											<path fill-rule="evenodd" clip-rule="evenodd" d="M11.5 8h-9V6h9v2z"/>
										</svg>
									</button>
								</div>
							</div>
						{/each}
					</div>
				{/if}
				{#if sourceError}
					<div class="w-full leading-none mt-1">
						<small class="text-red-500">{sourceError}</small>
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
