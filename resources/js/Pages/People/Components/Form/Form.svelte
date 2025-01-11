<script lang="ts">
	import { slide } from 'svelte/transition';
	import { useForm } from '@inertiajs/svelte';
	import { route } from 'ziggy-js';
	import { Sex, type EditPersonResource } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import * as Form from '@/Components/Forms';
	import Button from '@/Components/Primitives/Button.svelte';
	import Link from '@/Components/Primitives/Link.svelte';
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

<form {onsubmit} class="p-6 bg-white rounded-lg shadow-sm overflow-hidden">
	<div>
		<fieldset class="space-y-5">
			<Form.Field error={$form.errors.sex}>
				<Form.Label legend>{t('people.sex')}</Form.Label>
				<Form.RadioGroup bind:value={$form.sex}>
					<Form.Radio value="xy">{t('people.male')}</Form.Radio>
					<Form.Radio value="xx">{t('people.female')}</Form.Radio>
					<Form.Radio value={null}>{t('people.unknown')}</Form.Radio>
				</Form.RadioGroup>
			</Form.Field>

			<div class="flex flex-col gap-5 lg:flex-row">
				<div class="flex flex-col gap-5 sm:flex-row lg:w-1/2">
					<Form.Field error={$form.errors.name}>
						<Form.Label>{t('people.name')}</Form.Label>
						<Form.Input bind:value={$form.name}/>
					</Form.Field>
					<Form.Field error={$form.errors.middle_name}>
						<Form.Label>{t('people.middle_name')}</Form.Label>
						<Form.Input bind:value={$form.middle_name}/>
					</Form.Field>
				</div>
				<div class="flex w-full flex-col gap-5 sm:flex-row lg:w-1/2">
					<Form.Field error={$form.errors.family_name}>
						<Form.Label>{t('people.family_name')}</Form.Label>
						<Form.Input bind:value={$form.family_name}/>
					</Form.Field>
					<Form.Field error={$form.errors.last_name}>
						<Form.Label>{t('people.last_name')}</Form.Label>
						<Form.Input bind:value={$form.last_name}/>
					</Form.Field>
				</div>
			</div>
		</fieldset>

		<hr class="mt-7 mb-6 text-gray-200">

		<div class="w-full mb-4">
			<div class="font-medium text-xl text-gray-900">{t('people.external_links')}</div>
		</div>
		<fieldset class="space-y-5 md:space-y-0 md:space-x-5 flex flex-col md:flex-row">
			<div class="w-full md:w-1/2 flex flex-col">
				<label for="id_wielcy" class="w-full font-medium pb-1 text-gray-700">
					{t('people.id_in')}
					<Link href="http://www.wielcy.pl/" external>wielcy.pl</Link>
				</label>
				<div class="w-full flex">
					<input
						type="text"
						id="id_wielcy"
						bind:value={$form.id_wielcy}
						class="form-input rounded-r-none w-1/4 md:w-3/12 z-10"
						class:invalid={$form.errors.id_wielcy}
					>
					<input
						type="text"
						id="wielcy_search"
						placeholder={t('misc.coming_soon')}
						disabled
						class="form-input rounded-l-none -ml-px w-3/4 md:w-9/12"
					>
				</div>
				<Form.Error error={$form.errors.id_wielcy}/>
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
		<fieldset class="gap-5 flex flex-col sm:flex-row">
			<Form.Field error={$form.errors.birth_place}>
				<Form.Label>{t('misc.place')}</Form.Label>
				<Form.Input bind:value={$form.birth_place}/>
			</Form.Field>
			<Form.DateRangePicker
				label={t('misc.date.date')}
				bind:from={$form.birth_date_from}
				bind:to={$form.birth_date_to}
				errorFrom={$form.errors.birth_date_from ?? null}
				errorTo={$form.errors.birth_date_to ?? null}
			/>
		</fieldset>

		<hr class="mt-7 mb-6 text-gray-200">

		<div class="mb-4">
			<Form.Checkbox bind:checked={$form.dead}>
				<span class="font-medium text-xl text-gray-900">
					{$form.sex === 'xx' ? t('people.dead.xx') : t('people.dead.xy')}
				</span>
			</Form.Checkbox>
		</div>

		{#if $form.dead}
			<fieldset class="space-y-5" transition:slide>
				<div class="w-full lg:w-1/2 lg:pr-2.5">
					<Form.Field error={$form.errors.death_cause}>
						<Form.Label>{t('people.death_cause')}</Form.Label>
						<Form.Input bind:value={$form.death_cause}/>
					</Form.Field>
				</div>

				<div class="gap-5 flex flex-col sm:flex-row">
					<Form.Field error={$form.errors.death_place}>
						<Form.Label>{t('people.death_place')}</Form.Label>
						<Form.Input bind:value={$form.death_place}/>
					</Form.Field>
					<Form.DateRangePicker
						label={t('people.death_date')}
						bind:from={$form.death_date_from}
						bind:to={$form.death_date_to}
						errorFrom={$form.errors.death_date_from ?? null}
						errorTo={$form.errors.death_date_to ?? null}
					/>
				</div>

				<div class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
					<Form.Field error={$form.errors.funeral_place}>
						<Form.Label>{t('people.funeral_place')}</Form.Label>
						<Form.Input bind:value={$form.funeral_place}/>
					</Form.Field>
					<Form.DateRangePicker
						label={t('people.funeral_date')}
						bind:from={$form.funeral_date_from}
						bind:to={$form.funeral_date_to}
						errorFrom={$form.errors.funeral_date_from ?? null}
						errorTo={$form.errors.funeral_date_to ?? null}
					/>
				</div>

				<div class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
					<Form.Field error={$form.errors.burial_place}>
						<Form.Label>{t('people.burial_place')}</Form.Label>
						<Form.Input bind:value={$form.burial_place}/>
					</Form.Field>
					<Form.DateRangePicker
						label={t('people.burial_date')}
						bind:from={$form.burial_date_from}
						bind:to={$form.burial_date_to}
						errorFrom={$form.errors.burial_date_from ?? null}
						errorTo={$form.errors.burial_date_to ?? null}
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
				<Form.Field error={$form.errors.mother_id}>
					<Form.Label>{t('people.mother')}</Form.Label>
					<Form.PersonPicker
						bind:value={$form.mother_id}
						initialValue={person.motherId}
						sex={Sex.FEMALE}
						nullable
					/>
				</Form.Field>
				<Form.Field error={$form.errors.father_id}>
					<Form.Label>{t('people.father')}</Form.Label>
					<Form.PersonPicker
						bind:value={$form.father_id}
						initialValue={person.fatherId}
						sex={Sex.MALE}
						nullable
					/>
				</Form.Field>
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
					focus:outline-none focus:ring-3 focus:ring-blue-500/50
					active:border-blue-600 active:bg-blue-600 active:text-blue-100"
				aria-label="Add source"
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
								<Form.Input bind:value={$form.sources[index]}/>
								<div>
									<button
										type="button"
										onclick={() => $form.sources = [...$form.sources.slice(0, index), ...$form.sources.slice(index + 1)]}
										class="size-6 p-1 rounded-full border border-blue-700 text-blue-700 transition
											hover:bg-blue-100 hover:text-blue-800
											focus:outline-none focus:ring-3 focus:ring-blue-500/50
											active:bg-blue-600 active:border-blue-600 active:text-blue-100"
										aria-label="Remove source"
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
				<Form.Error error={sourceError}/>
			</div>
		</fieldset>

		<div class="-m-6 mt-6 px-6 py-4 bg-gray-50 flex justify-end">
			<Button type="submit">{t('misc.save')}</Button>
		</div>
	</div>
</form>
