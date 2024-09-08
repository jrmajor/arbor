<script lang="ts">
	import { slide } from 'svelte/transition';
	import { useForm } from '@inertiajs/svelte';
	import { route } from 'ziggy-js';
	import { RITES, EVENT_TYPES, type EditMarriageResource } from '@/types/resources/marriages';
	import { Sex } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import * as Form from '@/Components/Forms';
	import Button from '@/Components/Primitives/Button.svelte';

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
			<div class="flex flex-col gap-5 sm:flex-row">
				<Form.Field error={$form.errors.woman_id}>
					<Form.Label>{t('marriages.woman')}</Form.Label>
					<Form.PersonPicker
						bind:value={$form.woman_id}
						initialValue={marriage.woman.id}
						sex={Sex.FEMALE}
						disabled={action === 'edit'}
					/>
				</Form.Field>
				<Form.Field error={$form.errors.man_id}>
					<Form.Label>{t('marriages.man')}</Form.Label>
					<Form.PersonPicker
						bind:value={$form.man_id}
						initialValue={marriage.man.id}
						sex={Sex.MALE}
						disabled={action === 'edit'}
					/>
				</Form.Field>
			</div>

			<div class="flex w-full flex-col gap-5 sm:flex-row">
				<div class="flex grow basis-full flex-row gap-5">
					<Form.Field error={$form.errors.woman_order}>
						<Form.Label>{t('marriages.woman_order')}</Form.Label>
						<Form.Input bind:value={$form.woman_order}/>
					</Form.Field>
					<Form.Field error={$form.errors.man_order}>
						<Form.Label>{t('marriages.man_order')}</Form.Label>
						<Form.Input bind:value={$form.man_order}/>
					</Form.Field>
				</div>

				<Form.Field error={$form.errors.rite}>
					<Form.Label>{t('marriages.rite')}</Form.Label>
					<Form.Select bind:value={$form.rite}>
						<option value={null}>b/d</option>
						{#each RITES as rite}
							<option value={rite}>{t(`marriages.rites.${rite}`)}</option>
						{/each}
					</Form.Select>
				</Form.Field>
			</div>
		</fieldset>

		<hr class="mt-7 mb-6 text-gray-200">

		<div class="w-full mb-4">
			<div class="font-medium text-xl text-gray-900">{t('marriages.first_event')}</div>
		</div>
		<fieldset class="flex flex-col gap-5 lg:flex-row">
			<div class="w-full lg:w-1/3">
				<Form.Field error={$form.errors.first_event_type}>
					<Form.Label>{t('marriages.event_type')}</Form.Label>
					<Form.Select bind:value={$form.first_event_type}>
						<option value={null}>b/d</option>
						{#each EVENT_TYPES as type}
							<option value={type}>{t(`marriages.event_types.${type}`)}</option>
						{/each}
					</Form.Select>
				</Form.Field>
			</div>
			<div class="flex w-full flex-col gap-5 sm:flex-row lg:w-2/3">
				<Form.Field error={$form.errors.first_event_place}>
					<Form.Label>{t('misc.place')}</Form.Label>
					<Form.Input bind:value={$form.first_event_place}/>
				</Form.Field>
				<Form.DateRangePicker
					label={t('misc.date.date')}
					bind:from={$form.first_event_date_from}
					bind:to={$form.first_event_date_to}
					errorFrom={$form.errors.first_event_date_from ?? null}
					errorTo={$form.errors.first_event_date_to ?? null}
				/>
			</div>
		</fieldset>

		<hr class="mt-7 mb-6 text-gray-200">

		<div class="w-full mb-4">
			<div class="font-medium text-xl text-gray-900">{t('marriages.second_event')}</div>
		</div>
		<fieldset class="flex flex-col gap-5 lg:flex-row">
			<div class="w-full lg:w-1/3">
				<Form.Field error={$form.errors.second_event_type}>
					<Form.Label>{t('marriages.event_type')}</Form.Label>
					<Form.Select bind:value={$form.second_event_type}>
						<option value={null}>b/d</option>
						{#each EVENT_TYPES as type}
							<option value={type}>{t(`marriages.event_types.${type}`)}</option>
						{/each}
					</Form.Select>
				</Form.Field>
			</div>
			<div class="flex w-full flex-col gap-5 sm:flex-row lg:w-2/3">
				<Form.Field error={$form.errors.second_event_place}>
					<Form.Label>{t('misc.place')}</Form.Label>
					<Form.Input bind:value={$form.second_event_place}/>
				</Form.Field>
				<Form.DateRangePicker
					label={t('misc.date.date')}
					bind:from={$form.second_event_date_from}
					bind:to={$form.second_event_date_to}
					errorFrom={$form.errors.second_event_date_from ?? null}
					errorTo={$form.errors.second_event_date_to ?? null}
				/>
			</div>
		</fieldset>

		<hr class="mt-7 mb-6 text-gray-200">

		<div class="mb-4">
			<Form.Checkbox bind:checked={$form.divorced}>
				<span class="font-medium text-xl text-gray-900">
					{t('marriages.divorce')}
				</span>
			</Form.Checkbox>
		</div>

		{#if $form.divorced}
			<fieldset class="space-y-5 sm:space-y-0 sm:space-x-5 flex flex-col sm:flex-row" transition:slide>
				<Form.Field error={$form.errors.divorce_place}>
					<Form.Label>{t('misc.place')}</Form.Label>
					<Form.Input bind:value={$form.divorce_place}/>
				</Form.Field>
				<Form.DateRangePicker
					label={t('misc.date.date')}
					bind:from={$form.divorce_date_from}
					bind:to={$form.divorce_date_to}
					errorFrom={$form.errors.divorce_date_from ?? null}
					errorTo={$form.errors.divorce_date_to ?? null}
				/>
			</fieldset>
		{/if}

		<div class="-m-6 mt-6 px-6 py-4 bg-gray-50 flex justify-end">
			<Button type="submit">{t('misc.save')}</Button>
		</div>
	</div>
</form>
