<script lang="ts">
	import { route } from 'ziggy-js';
	import { inertia, useForm } from '@inertiajs/svelte';
	import { t } from '@/helpers/translations';
	import { authLayoutTitle } from '@/helpers/context';

	let { token, email, errors }: {
		token: string;
		email: string;
	} & SharedProps = $props();

	const title = authLayoutTitle.get();
	$title = t('passwords.password_reset');

	const form = useForm({ token, email, password: '', password_confirmation: '' });

	function onsubmit(event: SubmitEvent) {
		event.preventDefault();
		$form.post(route('password.store'));
	}
</script>

<svelte:head>
	<title>{t('passwords.password_reset')} - Arbor</title>
</svelte:head>

<form {onsubmit}>
	<div class="flex flex-wrap">
		<div class="w-full">
			<input
				type="text"
				bind:value={$form.email}
				autocomplete="email"
				placeholder={t('passwords.email')}
				class="form-input w-full"
				class:invalid={errors.email}
			>
		</div>
	</div>
	{#if errors.email}
		<div class="w-full leading-none mt-1 text-left">
			<small class="text-red-500">{errors.email}</small>
		</div>
	{/if}

	<div class="flex flex-wrap mt-4">
		<div class="w-full sm:w-1/2 sm:pr-1 pb-2 sm:pb-0">
			<!-- svelte-ignore a11y_autofocus -->
			<input
				autofocus
				type="password"
				bind:value={$form.password}
				autocomplete="new-password"
				placeholder={t('passwords.password').toLowerCase()}
				class="form-input w-full"
				class:invalid={errors.password}
			>
		</div>
		<div class="w-full sm:w-1/2 sm:pl-1">
			<input
				type="password"
				bind:value={$form.password_confirmation}
				autocomplete="current-password"
				placeholder={t('passwords.confirm_password').toLowerCase()}
				class="form-input w-full"
				class:invalid={errors.password}
			>
		</div>
	</div>
	{#if errors.password}
		<div class="w-full leading-none mt-1 text-left">
			<small class="text-red-500">{errors.password}</small>
		</div>
	{/if}

	<div class="mt-4 flex justify-between items-center">
		<div>
			<a use:inertia href={route('people.index')} class="a mr-1">
				<small>{t('misc.cancel')}</small>
			</a>
		</div>
		<div>
			<button type="submit" class="btn ml-1">
				{t('passwords.reset_password')}
			</button>
		</div>
	</div>
</form>
