<script lang="ts">
	import { route } from 'ziggy-js';
	import { useForm } from '@inertiajs/svelte';
	import { t } from '@/helpers/translations';
	import Button from '@/Components/Primitives/Button.svelte';
	import * as Form from '@/Components/Forms';

	let { user }: SharedProps = $props();

	user = user!;

	const emailForm = useForm({ email: user.email });
	const passwordForm = useForm({ password: '', password_confirmation: '' });
	const logoutForm = useForm({ password: '' });

	function submitEmail(event: SubmitEvent) {
		event.preventDefault();
		$emailForm.put(route('settings.updateEmail'));
	}

	function submitPassword(event: SubmitEvent) {
		event.preventDefault();
		$passwordForm.put(route('settings.updatePassword'));
	}

	function submitLogout(event: SubmitEvent) {
		event.preventDefault();
		$logoutForm.post(route('settings.logoutOtherDevices'));
	}
</script>

<svelte:head>
	<title>{t('settings.settings')} - Arbor</title>
</svelte:head>

<h1 class="mb-3 leading-none text-3xl font-medium">
	{t('settings.user')}: {user.username}
</h1>

<main class="p-6 bg-white rounded-lg shadow">
	<form onsubmit={submitEmail} class="flex flex-col lg:flex-row">
		<h2 class="w-full lg:w-1/3 lg:w-1/4 font-medium text-xl text-gray-900 mb-4 sm:mb-0">
			{t('settings.email')}
		</h2>

		<div class="w-full lg:w-2/3 lg:w-3/4">
			<div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-5 items-end sm:items-center">
				<input
					type="text"
					bind:value={$emailForm.email}
					class="form-input w-full sm:w-auto"
					class:invalid={$emailForm.errors.email}
				>
				<Button type="submit">{t('misc.save')}</Button>
			</div>
			<Form.Error error={$emailForm.errors.email}/>
		</div>
	</form>

	<hr class="mt-7 mb-6 text-gray-200">

	<form onsubmit={submitPassword} class="flex flex-col lg:flex-row">
		<h2 class="w-full lg:w-1/3 lg:w-1/4 font-medium text-xl text-gray-900 mb-4 sm:mb-0">
			{t('settings.change_password')}
		</h2>

		<div class="w-full lg:w-2/3 lg:w-3/4">
			<div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-5 items-end sm:items-center">
				<input
					type="password"
					bind:value={$passwordForm.password}
					placeholder={t('settings.password').toLowerCase()}
					class="form-input w-full sm:w-auto"
					class:invalid={$passwordForm.errors.password}
				>
				<input
					type="password"
					bind:value={$passwordForm.password_confirmation}
					placeholder={t('settings.confirm_password').toLowerCase()}
					class="form-input w-full sm:w-auto"
					class:invalid={$passwordForm.errors.password}
				>
				<Button type="submit">{t('misc.save')}</Button>
			</div>
			<Form.Error error={$passwordForm.errors.password}/>
		</div>
	</form>

	<hr class="mt-7 mb-6 text-gray-200">

	<form onsubmit={submitLogout} class="flex flex-col lg:flex-row">
		<h2 class="w-full lg:w-1/3 lg:w-1/4 font-medium text-xl text-gray-900 mb-4 sm:mb-0">
			{t('settings.logout_other_devices')}
		</h2>

		<div class="w-full lg:w-2/3 lg:w-3/4">
			<div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-5 items-end sm:items-center">
				<input
					type="password"
					bind:value={$logoutForm.password}
					placeholder={t('settings.password').toLowerCase()}
					class="form-input w-full sm:w-auto"
					class:invalid={$logoutForm.errors.password}
				>
				<Button type="submit">{t('settings.logout')}</Button>
			</div>
			<Form.Error error={$logoutForm.errors.password}/>
		</div>
	</form>
</main>
