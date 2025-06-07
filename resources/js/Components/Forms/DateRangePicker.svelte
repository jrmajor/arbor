<script lang="ts">
	import {
		format,
		isLastDayOfMonth,
		isSameDay,
		isSameMonth,
		isSameYear,
		isValid,
		lastDayOfMonth,
		lastDayOfYear,
		startOfMonth,
		startOfYear,
	} from 'date-fns';
	import { t } from '@/helpers/translations';
	import Error from './Error.svelte';

	let {
		label,
		from = $bindable(),
		to = $bindable(),
		errorFrom,
		errorTo,
	}: {
		label: string;
		from: string | null;
		to: string | null;
		errorFrom: string | null;
		errorTo: string | null;
	} = $props();

	const id = $props.id();

	let simple = $state(simplePickerValue() ?? '');
	let dateIsValid = $state(true);
	let advancedPicker = $state(!canSimplePickerBeUsed());

	function canSimplePickerBeUsed() {
		if (!from || !to || from === to) {
			return true;
		}

		if (
			isSameDay(from, startOfYear(from))
			&& isSameDay(to, lastDayOfYear(to))
		) {
			return isSameYear(from, to);
		}

		if (
			isSameDay(from, startOfMonth(from))
			&& isLastDayOfMonth(to)
		) {
			return isSameMonth(from, to);
		}

		return false;
	}

	function simplePickerValue() {
		if (!from || !to) {
			return null;
		}

		if (isSameDay(from, to)) {
			return from;
		}

		if (
			isSameDay(from, startOfYear(from))
			&& isSameDay(to, lastDayOfYear(to))
			&& isSameYear(from, to)
		) {
			return format(from, 'yyyy');
		}

		if (
			isSameDay(from, startOfMonth(from))
			&& isLastDayOfMonth(to)
			&& isSameMonth(from, to)
		) {
			return format(from, 'yyyy-MM');
		}

		return null;
	}

	function onSimpleInput() {
		dateIsValid = true;

		simple = simple.trim();

		if (simple.length === 0) {
			from = to = '';
			return;
		}

		simple = simple.replace(/[^0-9-]/g, '');

		const matches = simple.match(/^([0-9]{4})(?:(-)(?:([0-9]{1,2})(?:(-)(?:([0-9]{1,2}))?)?)?)?$/);

		if (matches === null) return clearInvalidDate();

		const year = parseInt(matches[1]);
		const month = parseInt(matches[3]) - 1;
		const day = parseInt(matches[5]);
		// eslint-disable-next-line prefer-destructuring
		const rawDay = matches[5];

		const secondHyphen = matches[4] === '-' ? '-' : '';

		let date;

		if (!isNaN(day)) {
			if (
				isValid((date = new Date(year, month, day)))
				&& date.getDate() === day && date.getMonth() === month
			) {
				from = to = format(date, 'yyyy-MM-dd');
				if (day > 3) {
					simple = format(date, 'yyyy-MM-dd');
				} else {
					simple = format(date, 'yyyy-MM-') + rawDay;
				}
				return;
			}

			return clearInvalidDate();
		}

		if (!isNaN(month)) {
			if (
				isValid((date = new Date(year, month, 15)))
				&& date.getMonth() === month
			) {
				from = format(startOfMonth(date), 'yyyy-MM-dd');
				to = format(lastDayOfMonth(date), 'yyyy-MM-dd');
				if (month > 0 || secondHyphen === '-') {
					simple = format(date, 'yyyy-MM') + secondHyphen;
				}
				return;
			}

			return clearInvalidDate();
		}

		if (!isNaN(year)) {
			if (isValid((date = new Date(year, 5, 15)))) {
				from = format(startOfYear(date), 'yyyy-MM-dd');
				to = format(lastDayOfYear(date), 'yyyy-MM-dd');
				return;
			}

			return clearInvalidDate();
		}

		clearInvalidDate();
	}

	function onSimpleBlur() {
		if (simple.slice(-1) === '-') simple = simple.slice(0, -1);
	}

	function clearInvalidDate() {
		dateIsValid = false;
		from = to = '';
	}
</script>

<div class="flex grow basis-full flex-col">
	<div class="flex items-center pb-1">
		<label for={id} class="font-medium text-gray-700">{label}</label>
		<button
			type="button"
			onclick={() => advancedPicker = !advancedPicker}
			class="ml-2 text-sm leading-none text-blue-700 underline transition-colors duration-100 hover:text-blue-800 focus:text-blue-800"
		>
			<!-- todo: block simple picker when date is complex -->
			{advancedPicker ? t('misc.date.simple') : t('misc.date.advanced')}
		</button>
	</div>
	<div>
		<div class="flex flex-nowrap items-center justify-between">
			{#if advancedPicker}
				<div class="-mb-2 flex shrink grow flex-wrap">
					<div class="mb-2 mr-1 flex grow-0 items-center">
						<p class="text-gray-900">{t('misc.date.between')}</p>
						<input
							type="text"
							maxlength="10"
							{id}
							bind:value={from}
							placeholder={t('misc.date.format')}
							class="form-input ml-1 w-32 tabular-nums"
							class:invalid={errorFrom}
						>
					</div>
					<div class="mb-2 flex grow-0 items-center">
						<p class="text-gray-900">{t('misc.date.and')}</p>
						<input
							type="text"
							maxlength="10"
							bind:value={to}
							placeholder={t('misc.date.format')}
							class="form-input ml-1 w-32 tabular-nums"
							class:invalid={errorTo}
						>
					</div>
				</div>
			{:else}
				<div class="flex shrink grow">
					<div class="flex items-center">
						<input
							type="text"
							maxlength="10"
							{id}
							bind:value={simple}
							oninput={onSimpleInput}
							onblur={onSimpleBlur}
							placeholder={t('misc.date.format')}
							class="form-input w-32 tabular-nums"
							class:invalid={!dateIsValid}
						>
					</div>
				</div>
			{/if}
		</div>
		<Error error={errorFrom}/>
		<Error error={errorTo}/>
	</div>
</div>
