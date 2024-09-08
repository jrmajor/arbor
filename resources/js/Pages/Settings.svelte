<script lang="ts">
	import { useForm } from '@inertiajs/svelte';
	import { route } from 'ziggy-js';
	import { t } from '@/helpers/translations';
	import * as Form from '@/Components/Forms';
	import Button from '@/Components/Primitives/Button.svelte';

	let { user }: SharedProps = $props();

	const emailForm = useForm({ email: user!.email });
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
	{t('settings.user')}: {user!.username}
</h1>

<main class="p-6 bg-white rounded-lg shadow">
	<form onsubmit={submitEmail} class="flex flex-col">
		<h2 class="font-medium text-xl text-gray-900 mb-4">
			{t('settings.email')}
		</h2>

		<div>
			<div class="flex flex-col items-end gap-2 sm:flex-row sm:items-center sm:gap-5">
				<div class="flex w-full sm:w-64">
					<Form.Input bind:value={$emailForm.email}/>
				</div>
				<Button type="submit">{t('misc.save')}</Button>
			</div>
			<Form.Error error={$emailForm.errors.email}/>
		</div>
	</form>

	<hr class="mt-7 mb-6 text-gray-200">

	<form onsubmit={submitPassword} class="flex flex-col">
		<h2 class="font-medium text-xl text-gray-900 mb-4">
			{t('settings.change_password')}
		</h2>

		<div>
			<div class="flex flex-col items-end gap-2 sm:flex-row sm:items-center sm:gap-5">
				<div class="flex w-full sm:w-64">
					<Form.Input
						bind:value={$passwordForm.password}
						type="password"
						placeholder={t('settings.password').toLowerCase()}
						error={$passwordForm.errors.password}
					/>
				</div>
				<div class="flex w-full sm:w-64">
					<Form.Input
						bind:value={$passwordForm.password_confirmation}
						type="password"
						placeholder={t('settings.confirm_password').toLowerCase()}
						error={$passwordForm.errors.password}
					/>
				</div>
				<Button type="submit">{t('misc.save')}</Button>
			</div>
			<Form.Error error={$passwordForm.errors.password}/>
		</div>
	</form>

	<hr class="mt-7 mb-6 text-gray-200">

	<form onsubmit={submitLogout} class="flex flex-col">
		<h2 class="font-medium text-xl text-gray-900 mb-4">
			{t('settings.logout_other_devices')}
		</h2>

		<div>
			<div class="flex flex-col items-end gap-2 sm:flex-row sm:items-center sm:gap-5">
				<div class="flex w-full sm:w-64">
					<Form.Input
						bind:value={$logoutForm.password}
						type="password"
						placeholder={t('settings.password').toLowerCase()}
						error={$logoutForm.errors.password}
					/>
				</div>
				<Button type="submit">{t('settings.logout')}</Button>
			</div>
			<Form.Error error={$logoutForm.errors.password}/>
		</div>
	</form>
</main>
