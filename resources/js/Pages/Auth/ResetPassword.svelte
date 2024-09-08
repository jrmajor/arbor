<script lang="ts">
	import { useForm } from '@inertiajs/svelte';
	import { route } from 'ziggy-js';
	import { authLayoutTitle } from '@/helpers/context';
	import { t } from '@/helpers/translations';
	import * as Form from '@/Components/Forms';
	import Button from '@/Components/Primitives/Button.svelte';
	import Link from '@/Components/Primitives/Link.svelte';

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
			<Form.Input
				bind:value={$form.email}
				autocomplete="email"
				placeholder={t('passwords.email').toLowerCase()}
				error={errors.email}
			/>
		</div>
	</div>
	<Form.Error error={errors.email}/>

	<div class="flex flex-wrap mt-4">
		<div class="w-full sm:w-1/2 sm:pr-1 pb-2 sm:pb-0">
			<Form.Input
				bind:value={$form.password}
				type="password"
				autofocus
				autocomplete="new-password"
				placeholder={t('passwords.password').toLowerCase()}
				error={errors.password}
			/>
		</div>
		<div class="w-full sm:w-1/2 sm:pl-1">
			<Form.Input
				bind:value={$form.password_confirmation}
				type="password"
				autocomplete="current-password"
				placeholder={t('passwords.confirm_password').toLowerCase()}
				error={errors.password}
			/>
		</div>
	</div>
	<Form.Error error={errors.password}/>

	<div class="mt-4 flex items-center justify-between gap-2">
		<Link href={route('people.index')}>
			<small>{t('misc.cancel')}</small>
		</Link>
		<Button type="submit">
			{t('passwords.reset_password')}
		</Button>
	</div>
</form>
