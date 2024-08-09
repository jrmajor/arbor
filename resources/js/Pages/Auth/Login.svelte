<script lang="ts">
	import { route } from 'ziggy-js';
	import { useForm } from '@inertiajs/svelte';
	import { t } from '@/helpers/translations';
	import { authLayoutTitle } from '@/helpers/context';

	let { errors }: SharedProps = $props();

	const title = authLayoutTitle.get();
	$title = t('auth.signing_in');

	const form = useForm({ username: '', password: '', remember: false });

	function onsubmit(event: SubmitEvent) {
		event.preventDefault();
		$form.post(route('login'));
	}
</script>

<svelte:head>
	<title>{t('auth.signing_in')} - Arbor</title>
</svelte:head>

<form {onsubmit}>
	<div class="flex flex-wrap">
		<div class="w-full sm:w-1/2 sm:pr-1 pb-2 sm:pb-0">
			<!-- svelte-ignore a11y_autofocus -->
			<input
				autofocus
				type="text"
				bind:value={$form.username}
				autocomplete="username"
				placeholder={t('auth.username_or_email').toLowerCase()}
				class="form-input w-full"
				class:invalid={errors.username || errors.password}
			>
		</div>
		<div class="w-full sm:w-1/2 sm:pl-1">
			<input
				type="password"
				bind:value={$form.password}
				autocomplete="current-password"
				placeholder={t('auth.password').toLowerCase()}
				class="form-input w-full"
				class:invalid={errors.username || errors.password}
			>
		</div>
	</div>

	{#if errors.username}
		<div class="w-full leading-none mt-1 text-left">
			<small class="text-red-500">{errors.username}</small>
		</div>
	{/if}
	{#if errors.password}
		<div class="w-full leading-none mt-1 text-left">
			<small class="text-red-500">{errors.password}</small>
		</div>
	{/if}

	<div class="mt-4 flex flex-wrap justify-between items-center">
		<div class="mr-3 grow flex items-center" style:flex-grow="10">
			<input type="checkbox" bind:checked={$form.remember} id="remember" class="form-checkbox size-3.5">
			<label for="remember" class="ml-1"><small>{t('auth.remember')}</small></label>
		</div>

		<div class="grow flex items-center justify-between">
			<a href={route('password.request')} class="a mr-1">
				<small>{t('auth.forgot_password')}</small>
			</a>
			<button type="submit" class="btn ml-1">
				{t('auth.sign_in')}
			</button>
		</div>
	</div>
</form>
