<script lang="ts">
	import { slide } from 'svelte/transition';
	import { route } from 'ziggy-js';
	import { useForm } from '@inertiajs/svelte';
	import { Sex } from '@/types/resources/people';
	import { RITES, EVENT_TYPES, type EditMarriageResource } from '@/types/resources/marriages';
	import { t } from '@/helpers/translations';
	import DateRangePicker from '@/Components/DateRangePicker.svelte';
	import PersonPicker from '@/Components/PersonPicker.svelte';
	import Button from '@/Components/Primitives/Button.svelte';
	import * as Form from '@/Components/Forms';

	let { marriage, action }: {
		marriage: EditMarriageResource;
		action: 'create' | 'edit';
	} = $props();

	const form = useForm({
		woman_id: marriage.woman.id,
		man_id: marriage.man.id,
		woman_order: marriage.womanOrder,
		man_order: marriage.manOrder,
		rite: marriage.rite,
		first_event_type: marriage.firstEventType,
		first_event_date_from: marriage.firstEventDateFrom,
		first_event_date_to: marriage.firstEventDateTo,
		first_event_place: marriage.firstEventPlace,
		second_event_type: marriage.secondEventType,
		second_event_date_from: marriage.secondEventDateFrom,
		second_event_date_to: marriage.secondEventDateTo,
		second_event_place: marriage.secondEventPlace,
		divorced: marriage.divorced,
		divorce_date_from: marriage.divorceDateFrom,
		divorce_date_to: marriage.divorceDateTo,
		divorce_place: marriage.divorcePlace,
	});

	function onsubmit(event: SubmitEvent) {
		event.preventDefault();

		$form.transform((data) => ({
			_method: action === 'create' ? 'post' : 'put',
			...data,
		})).post(
			action === 'create'
				? route('marriages.store')
				: route('marriages.update', marriage),
		);
	}
</script>

<form {onsubmit} class="p-6 bg-white rounded-lg shadow overflow-hidden">
	<div>
		<fieldset class="space-y-5">
			<div class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
				<PersonPicker
					label={t('marriages.woman')}
					bind:value={$form.woman_id}
					initialValue={marriage.woman.id}
					sex={Sex.FEMALE}
					error={$form.errors.woman_id ?? null}
					class="w-full sm:w-1/2"
				/>
				<PersonPicker
					label={t('marriages.man')}
					bind:value={$form.man_id}
					initialValue={marriage.man.id}
					sex={Sex.MALE}
					error={$form.errors.man_id ?? null}
					class="w-full sm:w-1/2"
				/>
			</div>

			<div class="w-full space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row">
				<div class="flex flex-row space-x-5">
					<div class="flex flex-col">
						<label for="woman_order" class="w-full font-medium pb-1 text-gray-700">{t('marriages.woman_order')}</label>
						<div class="w-full">
							<input
								type="text"
								id="woman_order"
								bind:value={$form.woman_order}
								class="form-input w-full"
								class:invalid={$form.errors.woman_order}
							>
							<Form.Error error={$form.errors.woman_order}/>
						</div>
					</div>
					<div class="flex flex-col">
						<label for="man_order" class="w-full font-medium pb-1 text-gray-700">{t('marriages.man_order')}</label>
						<div class="w-full">
							<input
								type="text"
								id="man_order"
								bind:value={$form.man_order}
								class="form-input w-full"
								class:invalid={$form.errors.man_order}
							>
							<Form.Error error={$form.errors.man_order}/>
						</div>
					</div>
				</div>
				<div class="grow flex flex-col">
					<label for="rite" class="w-full font-medium pb-1 text-gray-700">{t('marriages.rite')}</label>
					<div class="w-full">
						<select id="rite" bind:value={$form.rite} class="form-select w-full">
							<option value={null}>b/d</option>
							{#each RITES as rite}
								<option value={rite}>{t(`marriages.rites.${rite}`)}</option>
							{/each}
						</select>
					</div>
				</div>
			</div>
		</fieldset>

		<hr class="mt-7 mb-6 text-gray-200">

		<div class="w-full mb-4">
			<div class="font-medium text-xl text-gray-900">{t('marriages.first_event')}</div>
		</div>
		<fieldset class="space-y-5 lg:space-y-0 lg:space-x-5 flex flex-col lg:flex-row">
			<div class="w-full lg:w-1/3 flex flex-col">
				<label for="first_event_type" class="w-full font-medium pb-1 text-gray-700">{t('marriages.event_type')}</label>
				<div class="w-full">
					<select id="first_event_type" bind:value={$form.first_event_type} class="form-select w-full">
						<option value={null}>b/d</option>
						{#each EVENT_TYPES as type}
							<option value={type}>{t(`marriages.event_types.${type}`)}</option>
						{/each}
					</select>
				</div>
			</div>
			<div class="space-y-5 sm:space-y-0 sm:space-x-5 w-full sm:w-full lg:w-2/3 flex flex-col sm:flex-row">
				<div class="w-full sm:w-1/2 flex flex-col">
					<label for="first_event_place" class="w-full font-medium pb-1 text-gray-700">{t('misc.place')}</label>
					<div class="w-full">
						<input
							type="text"
							id="first_event_place"
							bind:value={$form.first_event_place}
							class="form-input w-full"
							class:invalid={$form.errors.first_event_place}
						>
						<Form.Error error={$form.errors.first_event_place}/>
					</div>
				</div>
				<DateRangePicker
					label={t('misc.date.date')}
					bind:from={$form.first_event_date_from}
					bind:to={$form.first_event_date_to}
					errorFrom={$form.errors.first_event_date_from ?? null}
					errorTo={$form.errors.first_event_date_to ?? null}
					class="w-full sm:w-1/2"
				/>
			</div>
		</fieldset>

		<hr class="mt-7 mb-6 text-gray-200">

		<div class="w-full mb-4">
			<div class="font-medium text-xl text-gray-900">{t('marriages.second_event')}</div>
		</div>
		<fieldset class="space-y-5 lg:space-y-0 lg:space-x-5 flex flex-col lg:flex-row">
			<div class="w-full lg:w-1/3 flex flex-col">
				<label for="second_event_type" class="w-full font-medium pb-1 text-gray-700">{t('marriages.event_type')}</label>
				<div class="w-full">
					<select id="second_event_type" bind:value={$form.second_event_type} class="form-select w-full">
						<option value={null}>b/d</option>
						{#each EVENT_TYPES as type}
							<option value={type}>{t(`marriages.event_types.${type}`)}</option>
						{/each}
					</select>
				</div>
			</div>
			<div class="space-y-5 sm:space-y-0 sm:space-x-5 w-full sm:w-full lg:w-2/3 flex flex-col sm:flex-row">
				<div class="w-full sm:w-1/2 flex flex-col">
					<label for="second_event_place" class="w-full font-medium pb-1 text-gray-700">{t('misc.place')}</label>
					<div class="w-full">
						<input
							type="text"
							id="second_event_place"
							bind:value={$form.second_event_place}
							class="form-input w-full"
							class:invalid={$form.errors.second_event_place}
						>
						<Form.Error error={$form.errors.second_event_place}/>
					</div>
				</div>
				<DateRangePicker
					label={t('misc.date.date')}
					bind:from={$form.second_event_date_from}
					bind:to={$form.second_event_date_to}
					errorFrom={$form.errors.second_event_date_from ?? null}
					errorTo={$form.errors.second_event_date_to ?? null}
					class="w-full sm:w-1/2"
				/>
			</div>
		</fieldset>

		<hr class="mt-7 mb-6 text-gray-200">

		<div class="w-full flex items-center mb-4">
			<label for="divorced" class="font-medium text-xl text-gray-900">{t('marriages.divorce')}</label>
			<input
				type="checkbox"
				id="divorced"
				bind:checked={$form.divorced}
				class="form-checkbox ml-2 size-4"
			>
		</div>
		{#if $form.divorced}
			<fieldset class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row" transition:slide>
				<div class="w-full sm:w-1/2 flex flex-col">
					<label for="divorce_place" class="w-full font-medium pb-1 text-gray-700">{t('misc.place')}</label>
					<div class="w-full">
						<input
							type="text"
							id="divorce_place"
							bind:value={$form.divorce_place}
							class="form-input w-full"
							class:invalid={$form.errors.divorce_place}
						>
						<Form.Error error={$form.errors.divorce_place}/>
					</div>
				</div>
				<DateRangePicker
					label={t('misc.date.date')}
					bind:from={$form.divorce_date_from}
					bind:to={$form.divorce_date_to}
					errorFrom={$form.errors.divorce_date_from ?? null}
					errorTo={$form.errors.divorce_date_to ?? null}
					class="w-full sm:w-1/2"
				/>
			</fieldset>
		{/if}

		<div class="-m-6 mt-6 px-6 py-4 bg-gray-50 flex justify-end">
			<Button type="submit">{t('misc.save')}</Button>
		</div>
	</div>
</form>
